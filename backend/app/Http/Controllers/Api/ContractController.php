<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\StoreContractRequest;
use App\Http\Requests\Contract\UpdateContractRequest;
use App\Models\Contract;
use App\Models\ContractTemplate;
use App\Services\ContractService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{
    public function __construct(
        private ContractService $contractService
    ) {}

    /**
     * Display a listing of contracts.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $query = Contract::with(['template', 'parties', 'variables'])
            ->where('user_id', $user->id);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('template_id')) {
            $query->where('template_id', $request->template_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $contracts = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'contracts' => $contracts->items(),
            'pagination' => [
                'current_page' => $contracts->currentPage(),
                'last_page' => $contracts->lastPage(),
                'per_page' => $contracts->perPage(),
                'total' => $contracts->total(),
            ],
        ]);
    }

    /**
     * Store a newly created contract.
     */
    public function store(StoreContractRequest $request): JsonResponse
    {
        $user = Auth::user();
        
        try {
            $contract = $this->contractService->createContract($request->validated(), $user);
            
            return response()->json([
                'message' => 'Contract created successfully',
                'contract' => $contract->load(['template', 'parties', 'variables']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create contract',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified contract.
     */
    public function show(Contract $contract): JsonResponse
    {
        $user = Auth::user();
        
        if ($contract->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized access to contract',
            ], 403);
        }

        return response()->json([
            'contract' => $contract->load(['template', 'parties', 'variables', 'documents']),
        ]);
    }

    /**
     * Update the specified contract.
     */
    public function update(UpdateContractRequest $request, Contract $contract): JsonResponse
    {
        $user = Auth::user();
        
        if ($contract->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized access to contract',
            ], 403);
        }

        try {
            $updatedContract = $this->contractService->updateContract($contract, $request->validated());
            
            return response()->json([
                'message' => 'Contract updated successfully',
                'contract' => $updatedContract->load(['template', 'parties', 'variables']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update contract',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified contract.
     */
    public function destroy(Contract $contract): JsonResponse
    {
        $user = Auth::user();
        
        if ($contract->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized access to contract',
            ], 403);
        }

        try {
            $this->contractService->deleteContract($contract);
            
            return response()->json([
                'message' => 'Contract deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete contract',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate PDF for the specified contract.
     */
    public function generatePdf(Contract $contract): JsonResponse
    {
        $user = Auth::user();
        
        if ($contract->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized access to contract',
            ], 403);
        }

        try {
            $pdfUrl = $this->contractService->generatePdf($contract);
            
            return response()->json([
                'message' => 'PDF generated successfully',
                'pdf_url' => $pdfUrl,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate PDF',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sign the specified contract.
     */
    public function sign(Contract $contract): JsonResponse
    {
        $user = Auth::user();
        
        if ($contract->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized access to contract',
            ], 403);
        }

        if ($contract->is_signed) {
            return response()->json([
                'message' => 'Contract is already signed',
            ], 400);
        }

        try {
            $this->contractService->signContract($contract, $user);
            
            return response()->json([
                'message' => 'Contract signed successfully',
                'contract' => $contract->fresh()->load(['template', 'parties', 'variables']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to sign contract',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get contract statistics for the authenticated user.
     */
    public function statistics(): JsonResponse
    {
        $user = Auth::user();
        
        $stats = [
            'total_contracts' => Contract::where('user_id', $user->id)->count(),
            'active_contracts' => Contract::where('user_id', $user->id)->active()->count(),
            'signed_contracts' => Contract::where('user_id', $user->id)->signed()->count(),
            'expired_contracts' => Contract::where('user_id', $user->id)->expired()->count(),
            'draft_contracts' => Contract::where('user_id', $user->id)->where('status', 'draft')->count(),
        ];

        return response()->json([
            'statistics' => $stats,
        ]);
    }
}
