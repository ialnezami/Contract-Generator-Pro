<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\ContractVariable;
use Illuminate\Support\Str;

class ContractVariableService
{
    /**
     * Variable types and their validation rules
     */
    private array $variableTypes = [
        'text' => [
            'max_length' => 1000,
            'pattern' => '/^[a-zA-Z0-9\s\-_.,!?()]+$/',
        ],
        'number' => [
            'min' => -999999999,
            'max' => 999999999,
            'decimals' => 2,
        ],
        'date' => [
            'format' => 'Y-m-d',
            'min_date' => '1900-01-01',
            'max_date' => '2100-12-31',
        ],
        'email' => [
            'pattern' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        ],
        'phone' => [
            'pattern' => '/^[\+]?[1-9][\d]{0,15}$/',
        ],
        'currency' => [
            'min' => 0,
            'max' => 999999999.99,
            'decimals' => 2,
        ],
        'percentage' => [
            'min' => 0,
            'max' => 100,
            'decimals' => 2,
        ],
    ];

    /**
     * Replace variables in contract content
     */
    public function replaceVariables(string $content, array $variables): string
    {
        foreach ($variables as $variable) {
            $placeholder = '[' . $variable['name'] . ']';
            $value = $this->formatVariableValue($variable);
            $content = str_replace($placeholder, $value, $content);
        }

        return $content;
    }

    /**
     * Validate variable value based on type
     */
    public function validateVariable(array $variable): array
    {
        $errors = [];
        $type = $variable['type'] ?? 'text';
        $value = $variable['value'] ?? '';

        if (!isset($this->variableTypes[$type])) {
            $errors[] = "Invalid variable type: {$type}";
            return $errors;
        }

        $rules = $this->variableTypes[$type];

        switch ($type) {
            case 'text':
                if (strlen($value) > $rules['max_length']) {
                    $errors[] = "Text too long. Maximum {$rules['max_length']} characters allowed.";
                }
                if (!preg_match($rules['pattern'], $value)) {
                    $errors[] = "Text contains invalid characters.";
                }
                break;

            case 'number':
                if (!is_numeric($value)) {
                    $errors[] = "Value must be a valid number.";
                } else {
                    if ($value < $rules['min'] || $value > $rules['max']) {
                        $errors[] = "Number must be between {$rules['min']} and {$rules['max']}.";
                    }
                }
                break;

            case 'date':
                if (!$this->isValidDate($value, $rules['format'])) {
                    $errors[] = "Invalid date format. Use {$rules['format']}.";
                } else {
                    $date = \DateTime::createFromFormat($rules['format'], $value);
                    if ($date < \DateTime::createFromFormat($rules['format'], $rules['min_date']) ||
                        $date > \DateTime::createFromFormat($rules['format'], $rules['max_date'])) {
                        $errors[] = "Date must be between {$rules['min_date']} and {$rules['max_date']}.";
                    }
                }
                break;

            case 'email':
                if (!preg_match($rules['pattern'], $value)) {
                    $errors[] = "Invalid email format.";
                }
                break;

            case 'phone':
                if (!preg_match($rules['pattern'], $value)) {
                    $errors[] = "Invalid phone number format.";
                }
                break;

            case 'currency':
                if (!is_numeric($value) || $value < $rules['min'] || $value > $rules['max']) {
                    $errors[] = "Currency amount must be between {$rules['min']} and {$rules['max']}.";
                }
                break;

            case 'percentage':
                if (!is_numeric($value) || $value < $rules['min'] || $value > $rules['max']) {
                    $errors[] = "Percentage must be between {$rules['min']} and {$rules['max']}.";
                }
                break;
        }

        return $errors;
    }

    /**
     * Extract variables from template content
     */
    public function extractVariablesFromTemplate(string $content): array
    {
        preg_match_all('/\[([^\]]+)\]/', $content, $matches);
        
        $variables = [];
        if (isset($matches[1])) {
            foreach ($matches[1] as $variableName) {
                $variables[] = [
                    'name' => trim($variableName),
                    'type' => $this->detectVariableType($variableName),
                    'required' => true,
                    'description' => $this->generateVariableDescription($variableName),
                ];
            }
        }

        return $variables;
    }

    /**
     * Detect variable type based on name
     */
    private function detectVariableType(string $variableName): string
    {
        $name = strtolower($variableName);

        if (Str::contains($name, ['date', 'start', 'end', 'expir'])) {
            return 'date';
        }
        if (Str::contains($name, ['email', 'mail'])) {
            return 'email';
        }
        if (Str::contains($name, ['phone', 'tel', 'mobile'])) {
            return 'phone';
        }
        if (Str::contains($name, ['amount', 'price', 'cost', 'value', 'total'])) {
            return 'currency';
        }
        if (Str::contains($name, ['rate', 'percentage', 'percent'])) {
            return 'percentage';
        }
        if (Str::contains($name, ['quantity', 'count', 'number'])) {
            return 'number';
        }

        return 'text';
    }

    /**
     * Generate variable description
     */
    private function generateVariableDescription(string $variableName): string
    {
        $name = str_replace(['_', '-'], ' ', $variableName);
        return ucwords($name);
    }

    /**
     * Format variable value for display
     */
    private function formatVariableValue(array $variable): string
    {
        $value = $variable['value'];
        $type = $variable['type'] ?? 'text';

        switch ($type) {
            case 'date':
                if ($this->isValidDate($value)) {
                    return date('F j, Y', strtotime($value));
                }
                break;

            case 'currency':
                if (is_numeric($value)) {
                    return '$' . number_format($value, 2);
                }
                break;

            case 'percentage':
                if (is_numeric($value)) {
                    return number_format($value, 2) . '%';
                }
                break;

            case 'number':
                if (is_numeric($value)) {
                    return number_format($value);
                }
                break;
        }

        return $value;
    }

    /**
     * Check if date is valid
     */
    private function isValidDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Get variable constraints for a specific type
     */
    public function getVariableConstraints(string $type): array
    {
        return $this->variableTypes[$type] ?? [];
    }

    /**
     * Get all available variable types
     */
    public function getAvailableVariableTypes(): array
    {
        return array_keys($this->variableTypes);
    }

    /**
     * Process contract variables and update content
     */
    public function processContractVariables(Contract $contract): void
    {
        $template = $contract->template;
        $content = $template->content;

        // Replace variables in content
        $variables = $contract->variables->map(function ($variable) {
            return [
                'name' => $variable->name,
                'type' => $variable->type,
                'value' => $variable->value,
            ];
        })->toArray();

        $processedContent = $this->replaceVariables($content, $variables);

        // Update contract content
        $contract->update(['content' => $processedContent]);
    }
}
