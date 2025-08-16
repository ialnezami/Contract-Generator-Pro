<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ContractService
{
    /**
     * Create a new contract.
     */
    public function createContract(array $data, User $user): Contract
    {
        return DB::transaction(function () use ($data, $user) {
            // Create the contract
            $contract = Contract::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'content' => $data['content'] ?? '',
                'status' => $data['status'] ?? 'draft',
                'template_id' => $data['template_id'],
                'user_id' => $user->id,
                'generated_at' => now(),
                'expires_at' => $data['expires_at'] ?? null,
                'total_value' => $data['total_value'] ?? null,
                'currency' => $data['currency'] ?? 'USD',
                'metadata' => $data['metadata'] ?? [],
            ]);

            // Create contract variables if provided
            if (isset($data['variables'])) {
                foreach ($data['variables'] as $variable) {
                    $contract->variables()->create([
                        'name' => $variable['name'],
                        'type' => $variable['type'] ?? 'text',
                        'value' => $variable['value'],
                    ]);
                }
            }

            // Create contract parties if provided
            if (isset($data['parties'])) {
                foreach ($data['parties'] as $party) {
                    $contract->parties()->create([
                        'name' => $party['name'],
                        'type' => $party['type'],
                        'email' => $party['email'] ?? null,
                        'phone' => $party['phone'] ?? null,
                        'address' => $party['address'] ?? null,
                        'city' => $party['city'] ?? null,
                        'state' => $party['state'] ?? null,
                        'zip_code' => $party['zip_code'] ?? null,
                        'country' => $party['country'] ?? null,
                        'tax_id' => $party['tax_id'] ?? null,
                        'metadata' => $party['metadata'] ?? [],
                    ]);
                }
            }

            // Generate contract content from template
            $this->generateContractContent($contract);

            return $contract;
        });
    }

    /**
     * Update an existing contract.
     */
    public function updateContract(Contract $contract, array $data): Contract
    {
        return DB::transaction(function () use ($contract, $data) {
            // Update contract fields
            $contract->update($data);

            // Update contract variables if provided
            if (isset($data['variables'])) {
                // Delete existing variables
                $contract->variables()->delete();
                
                // Create new variables
                foreach ($data['variables'] as $variable) {
                    $contract->variables()->create([
                        'name' => $variable['name'],
                        'type' => $variable['type'] ?? 'text',
                        'value' => $variable['value'],
                    ]);
                }
            }

            // Update contract parties if provided
            if (isset($data['parties'])) {
                // Delete existing parties
                $contract->parties()->delete();
                
                // Create new parties
                foreach ($data['parties'] as $party) {
                    $contract->parties()->create([
                        'name' => $party['name'],
                        'type' => $party['type'],
                        'email' => $party['email'] ?? null,
                        'phone' => $party['phone'] ?? null,
                        'address' => $party['address'] ?? null,
                        'city' => $party['city'] ?? null,
                        'state' => $party['state'] ?? null,
                        'zip_code' => $party['zip_code'] ?? null,
                        'country' => $party['country'] ?? null,
                        'tax_id' => $party['tax_id'] ?? null,
                        'metadata' => $party['metadata'] ?? [],
                    ]);
                }
            }

            // Regenerate contract content if variables changed
            if (isset($data['variables'])) {
                $this->generateContractContent($contract);
            }

            return $contract;
        });
    }

    /**
     * Delete a contract.
     */
    public function deleteContract(Contract $contract): void
    {
        DB::transaction(function () use ($contract) {
            // Delete associated documents
            foreach ($contract->documents as $document) {
                if (Storage::exists($document->file_path)) {
                    Storage::delete($document->file_path);
                }
            }

            // Delete the contract (this will cascade delete related records)
            $contract->delete();
        });
    }

    /**
     * Generate PDF for a contract.
     */
    public function generatePdf(Contract $contract): string
    {
        // Generate PDF using DomPDF
        $pdf = PDF::loadView('contracts.pdf', [
            'contract' => $contract->load(['template', 'parties', 'variables']),
        ]);

        // Set PDF options
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        // Generate filename
        $filename = 'contract_' . $contract->id . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        $filepath = 'contracts/' . $filename;

        // Store PDF
        Storage::put('public/' . $filepath, $pdf->output());

        // Create document record
        $contract->documents()->create([
            'name' => 'Contract PDF',
            'type' => 'pdf',
            'file_path' => $filepath,
            'file_name' => $filename,
            'mime_type' => 'application/pdf',
            'file_size' => Storage::size('public/' . $filepath),
            'version' => '1.0',
            'description' => 'Generated PDF version of the contract',
        ]);

        return Storage::url($filepath);
    }

    /**
     * Sign a contract.
     */
    public function signContract(Contract $contract, User $user): void
    {
        $contract->update([
            'is_signed' => true,
            'signed_at' => now(),
            'signed_by' => $user->name,
            'status' => 'active',
        ]);

        // Log the signing activity
        activity()
            ->performedOn($contract)
            ->causedBy($user)
            ->log('Contract signed');
    }

    /**
     * Generate contract content from template.
     */
    private function generateContractContent(Contract $contract): void
    {
        $template = $contract->template;
        $content = $template->content;

        // Replace variables in the content
        foreach ($contract->variables as $variable) {
            $placeholder = '[' . $variable->name . ']';
            $content = str_replace($placeholder, $variable->value, $content);
        }

        // Update contract content
        $contract->update(['content' => $content]);
    }

    /**
     * Get contract statistics for a user.
     */
    public function getStatistics(User $user): array
    {
        return [
            'total_contracts' => Contract::where('user_id', $user->id)->count(),
            'active_contracts' => Contract::where('user_id', $user->id)->active()->count(),
            'signed_contracts' => Contract::where('user_id', $user->id)->signed()->count(),
            'expired_contracts' => Contract::where('user_id', $user->id)->expired()->count(),
            'draft_contracts' => Contract::where('user_id', $user->id)->where('status', 'draft')->count(),
            'total_value' => Contract::where('user_id', $user->id)->sum('total_value'),
        ];
    }
}
