<?php

namespace Database\Factories;

use App\Models\ContractTemplate;
use App\Models\User;
use App\Models\TemplateCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContractTemplate>
 */
class ContractTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'content' => $this->generateSampleContent(),
            'category_id' => TemplateCategory::factory(),
            'is_public' => fake()->boolean(80), // 80% chance of being public
            'is_active' => true,
            'user_id' => User::factory(),
            'version' => '1.0',
            'tags' => json_encode(fake()->words(3)),
            'variables_schema' => json_encode($this->generateVariablesSchema()),
            'preview_image' => null,
            'usage_count' => fake()->numberBetween(0, 100),
            'rating' => fake()->randomFloat(1, 1, 5),
            'rating_count' => fake()->numberBetween(0, 50),
            'type' => fake()->randomElement(['general', 'employment', 'business', 'real_estate', 'service']),
            'price' => fake()->optional(0.3)->randomFloat(2, 9.99, 99.99), // 30% chance of having a price
            'approval_status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'rejection_reason' => null,
            'approved_by' => null,
            'approved_at' => null,
            'parent_version_id' => null,
            'is_latest_version' => true,
            'download_count' => fake()->numberBetween(0, 200),
            'favorite_count' => fake()->numberBetween(0, 100),
            'metadata' => json_encode([
                'estimated_completion_time' => fake()->randomElement(['1-2 days', '3-5 days', '1 week']),
                'difficulty_level' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
                'legal_jurisdiction' => fake()->randomElement(['US', 'UK', 'EU', 'Global']),
            ]),
        ];
    }

    /**
     * Generate sample contract content with variables
     */
    private function generateSampleContent(): string
    {
        $templates = [
            "This agreement is made between [company_name] and [client_name] on [start_date].\n\nThe total contract value is [contract_amount] and the project will be completed by [end_date].\n\nPayment terms: [payment_terms]% upfront, [payment_terms]% upon completion.",
            
            "SERVICE AGREEMENT\n\nProvider: [provider_name]\nClient: [client_name]\nEffective Date: [start_date]\n\nServices: [service_description]\nDuration: [contract_duration]\nRate: [hourly_rate] per hour\n\nThis agreement is valid until [end_date].",
            
            "EMPLOYMENT CONTRACT\n\nEmployee: [employee_name]\nEmployer: [company_name]\nPosition: [job_title]\nStart Date: [start_date]\nSalary: [annual_salary]\n\nTerms and conditions apply as outlined in the employee handbook.",
            
            "LEASE AGREEMENT\n\nProperty Address: [property_address]\nLandlord: [landlord_name]\nTenant: [tenant_name]\nLease Term: [lease_duration]\nMonthly Rent: [monthly_rent]\nSecurity Deposit: [security_deposit]\n\nThis lease begins on [start_date] and expires on [end_date].",
            
            "NON-DISCLOSURE AGREEMENT\n\nBetween [company_name] and [recipient_name]\n\nPurpose: [purpose_description]\nDuration: [nda_duration]\nEffective Date: [start_date]\n\nConfidential information includes [confidential_scope]."
        ];

        return fake()->randomElement($templates);
    }

    /**
     * Generate variables schema based on content
     */
    private function generateVariablesSchema(): array
    {
        $schemas = [
            [
                ['name' => 'company_name', 'type' => 'text', 'required' => true, 'description' => 'Company name'],
                ['name' => 'client_name', 'type' => 'text', 'required' => true, 'description' => 'Client name'],
                ['name' => 'start_date', 'type' => 'date', 'required' => true, 'description' => 'Contract start date'],
                ['name' => 'end_date', 'type' => 'date', 'required' => true, 'description' => 'Contract end date'],
                ['name' => 'contract_amount', 'type' => 'currency', 'required' => true, 'description' => 'Total contract value'],
                ['name' => 'payment_terms', 'type' => 'percentage', 'required' => true, 'description' => 'Payment percentage'],
            ],
            [
                ['name' => 'provider_name', 'type' => 'text', 'required' => true, 'description' => 'Service provider name'],
                ['name' => 'client_name', 'type' => 'text', 'required' => true, 'description' => 'Client name'],
                ['name' => 'start_date', 'type' => 'date', 'required' => true, 'description' => 'Service start date'],
                ['name' => 'service_description', 'type' => 'text', 'required' => true, 'description' => 'Description of services'],
                ['name' => 'contract_duration', 'type' => 'text', 'required' => true, 'description' => 'Contract duration'],
                ['name' => 'hourly_rate', 'type' => 'currency', 'required' => true, 'description' => 'Hourly rate'],
                ['name' => 'end_date', 'type' => 'date', 'required' => true, 'description' => 'Service end date'],
            ],
            [
                ['name' => 'employee_name', 'type' => 'text', 'required' => true, 'description' => 'Employee full name'],
                ['name' => 'company_name', 'type' => 'text', 'required' => true, 'description' => 'Company name'],
                ['name' => 'job_title', 'type' => 'text', 'required' => true, 'description' => 'Job position title'],
                ['name' => 'start_date', 'type' => 'date', 'required' => true, 'description' => 'Employment start date'],
                ['name' => 'annual_salary', 'type' => 'currency', 'required' => true, 'description' => 'Annual salary amount'],
            ],
            [
                ['name' => 'property_address', 'type' => 'text', 'required' => true, 'description' => 'Property address'],
                ['name' => 'landlord_name', 'type' => 'text', 'required' => true, 'description' => 'Landlord name'],
                ['name' => 'tenant_name', 'type' => 'text', 'required' => true, 'description' => 'Tenant name'],
                ['name' => 'lease_duration', 'type' => 'text', 'required' => true, 'description' => 'Lease term duration'],
                ['name' => 'monthly_rent', 'type' => 'currency', 'required' => true, 'description' => 'Monthly rent amount'],
                ['name' => 'security_deposit', 'type' => 'currency', 'required' => true, 'description' => 'Security deposit amount'],
                ['name' => 'start_date', 'type' => 'date', 'required' => true, 'description' => 'Lease start date'],
                ['name' => 'end_date', 'type' => 'date', 'required' => true, 'description' => 'Lease end date'],
            ],
            [
                ['name' => 'company_name', 'type' => 'text', 'required' => true, 'description' => 'Company name'],
                ['name' => 'recipient_name', 'type' => 'text', 'required' => true, 'description' => 'Recipient name'],
                ['name' => 'purpose_description', 'type' => 'text', 'required' => true, 'description' => 'Purpose of disclosure'],
                ['name' => 'nda_duration', 'type' => 'text', 'required' => true, 'description' => 'NDA duration'],
                ['name' => 'start_date', 'type' => 'date', 'required' => true, 'description' => 'NDA start date'],
                ['name' => 'confidential_scope', 'type' => 'text', 'required' => true, 'description' => 'Scope of confidentiality'],
            ],
        ];

        return fake()->randomElement($schemas);
    }

    /**
     * Indicate that the template is public
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
            'approval_status' => 'approved',
        ]);
    }

    /**
     * Indicate that the template is private
     */
    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => false,
            'approval_status' => 'pending',
        ]);
    }

    /**
     * Indicate that the template is premium (has a price)
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => fake()->randomFloat(2, 19.99, 199.99),
            'is_public' => true,
            'approval_status' => 'approved',
        ]);
    }

    /**
     * Indicate that the template is highly rated
     */
    public function highlyRated(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->randomFloat(1, 4.0, 5.0),
            'rating_count' => fake()->numberBetween(10, 100),
        ]);
    }

    /**
     * Indicate that the template is popular (high usage)
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'usage_count' => fake()->numberBetween(100, 1000),
            'download_count' => fake()->numberBetween(200, 2000),
            'favorite_count' => fake()->numberBetween(50, 500),
        ]);
    }
}
