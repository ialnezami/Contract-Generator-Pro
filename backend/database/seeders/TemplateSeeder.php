<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContractTemplate;
use App\Models\User;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@contractgeneratorpro.com')->first();

        // Service Agreement Template
        $serviceAgreement = ContractTemplate::create([
            'name' => 'Service Agreement',
            'description' => 'A comprehensive service agreement template for freelancers and service providers.',
            'content' => $this->getServiceAgreementContent(),
            'category' => 'Service',
            'is_public' => true,
            'is_active' => true,
            'user_id' => $admin->id,
            'version' => '1.0',
            'tags' => ['service', 'freelance', 'agreement', 'professional'],
            'variables_schema' => [
                'client_name' => 'text',
                'service_provider_name' => 'text',
                'service_description' => 'textarea',
                'start_date' => 'date',
                'end_date' => 'date',
                'payment_amount' => 'number',
                'payment_terms' => 'text',
            ],
            'usage_count' => 45,
            'rating' => 4.5,
        ]);

        // Employment Contract Template
        $employmentContract = ContractTemplate::create([
            'name' => 'Employment Contract',
            'description' => 'Standard employment contract template for hiring employees.',
            'content' => $this->getEmploymentContractContent(),
            'category' => 'Employment',
            'is_public' => true,
            'is_active' => true,
            'user_id' => $admin->id,
            'version' => '1.0',
            'tags' => ['employment', 'hiring', 'contract', 'HR'],
            'variables_schema' => [
                'employee_name' => 'text',
                'employer_name' => 'text',
                'job_title' => 'text',
                'start_date' => 'date',
                'salary' => 'number',
                'work_schedule' => 'text',
                'benefits' => 'textarea',
            ],
            'usage_count' => 32,
            'rating' => 4.3,
        ]);

        // NDA Template
        $nda = ContractTemplate::create([
            'name' => 'Non-Disclosure Agreement',
            'description' => 'Confidentiality agreement template for protecting sensitive information.',
            'content' => $this->getNDAContent(),
            'category' => 'Legal',
            'is_public' => true,
            'is_active' => true,
            'user_id' => $admin->id,
            'version' => '1.0',
            'tags' => ['NDA', 'confidentiality', 'legal', 'protection'],
            'variables_schema' => [
                'disclosing_party' => 'text',
                'receiving_party' => 'text',
                'confidential_information' => 'textarea',
                'duration' => 'text',
                'effective_date' => 'date',
            ],
            'usage_count' => 28,
            'rating' => 4.7,
        ]);

        // Partnership Agreement Template
        $partnership = ContractTemplate::create([
            'name' => 'Partnership Agreement',
            'description' => 'Business partnership agreement template for joint ventures.',
            'content' => $this->getPartnershipContent(),
            'category' => 'Business',
            'is_public' => true,
            'is_active' => true,
            'user_id' => $admin->id,
            'version' => '1.0',
            'tags' => ['partnership', 'business', 'joint venture', 'agreement'],
            'variables_schema' => [
                'partner1_name' => 'text',
                'partner2_name' => 'text',
                'business_name' => 'text',
                'partnership_percentage1' => 'number',
                'partnership_percentage2' => 'number',
                'business_purpose' => 'textarea',
                'start_date' => 'date',
            ],
            'usage_count' => 19,
            'rating' => 4.4,
        ]);

        // Rental Agreement Template
        $rental = ContractTemplate::create([
            'name' => 'Rental Agreement',
            'description' => 'Property rental agreement template for landlords and tenants.',
            'content' => $this->getRentalContent(),
            'category' => 'Real Estate',
            'is_public' => true,
            'is_active' => true,
            'user_id' => $admin->id,
            'version' => '1.0',
            'tags' => ['rental', 'property', 'lease', 'real estate'],
            'variables_schema' => [
                'landlord_name' => 'text',
                'tenant_name' => 'text',
                'property_address' => 'textarea',
                'rent_amount' => 'number',
                'security_deposit' => 'number',
                'lease_start' => 'date',
                'lease_end' => 'date',
            ],
            'usage_count' => 23,
            'rating' => 4.2,
        ]);
    }

    private function getServiceAgreementContent(): string
    {
        return "SERVICE AGREEMENT

This Service Agreement (the 'Agreement') is entered into on [start_date] by and between:

[client_name] (the 'Client')
and
[service_provider_name] (the 'Service Provider')

1. SERVICES
The Service Provider agrees to provide the following services: [service_description]

2. TERM
This Agreement shall commence on [start_date] and continue until [end_date] unless terminated earlier.

3. COMPENSATION
The Client shall pay the Service Provider [payment_amount] for the services rendered, payable according to the following terms: [payment_terms]

4. CONFIDENTIALITY
Both parties agree to maintain the confidentiality of any proprietary information shared during the course of this agreement.

5. TERMINATION
Either party may terminate this Agreement with 30 days written notice.

IN WITNESS WHEREOF, the parties have executed this Agreement as of the date first above written.

Client: _____________________
Date: _____________________

Service Provider: _____________________
Date: _____________________";
    }

    private function getEmploymentContractContent(): string
    {
        return "EMPLOYMENT CONTRACT

This Employment Contract (the 'Contract') is entered into on [start_date] between:

[employer_name] (the 'Employer')
and
[employee_name] (the 'Employee')

1. POSITION
The Employee shall serve as [job_title] and shall perform all duties and responsibilities associated with this position.

2. TERM
This Contract shall commence on [start_date] and shall continue until terminated by either party.

3. COMPENSATION
The Employee shall receive an annual salary of [salary], payable in accordance with the Employer's standard payroll schedule.

4. WORK SCHEDULE
The Employee shall work according to the following schedule: [work_schedule]

5. BENEFITS
The Employee shall be entitled to the following benefits: [benefits]

6. TERMINATION
Either party may terminate this Contract with 30 days written notice.

IN WITNESS WHEREOF, the parties have executed this Contract as of the date first above written.

Employer: _____________________
Date: _____________________

Employee: _____________________
Date: _____________________";
    }

    private function getNDAContent(): string
    {
        return "NON-DISCLOSURE AGREEMENT

This Non-Disclosure Agreement (the 'Agreement') is entered into on [effective_date] between:

[disclosing_party] (the 'Disclosing Party')
and
[receiving_party] (the 'Receiving Party')

1. CONFIDENTIAL INFORMATION
The Receiving Party acknowledges that it may receive confidential information from the Disclosing Party, including but not limited to: [confidential_information]

2. NON-DISCLOSURE
The Receiving Party agrees to maintain the confidentiality of the Confidential Information and not to disclose it to any third party.

3. TERM
This Agreement shall remain in effect for [duration] from the effective date.

4. RETURN OF MATERIALS
Upon termination of this Agreement, the Receiving Party shall return all Confidential Information to the Disclosing Party.

IN WITNESS WHEREOF, the parties have executed this Agreement as of the date first above written.

Disclosing Party: _____________________
Date: _____________________

Receiving Party: _____________________
Date: _____________________";
    }

    private function getPartnershipContent(): string
    {
        return "PARTNERSHIP AGREEMENT

This Partnership Agreement (the 'Agreement') is entered into on [start_date] between:

[partner1_name] (Partner 1)
and
[partner2_name] (Partner 2)

1. BUSINESS PURPOSE
The partners agree to form a partnership for the purpose of: [business_purpose]

2. PARTNERSHIP INTERESTS
Partner 1 shall have a [partnership_percentage1]% interest in the partnership.
Partner 2 shall have a [partnership_percentage2]% interest in the partnership.

3. BUSINESS NAME
The partnership shall operate under the name: [business_name]

4. PROFITS AND LOSSES
Profits and losses shall be shared in proportion to the partners' respective partnership interests.

5. MANAGEMENT
Both partners shall have equal rights in the management of the partnership business.

IN WITNESS WHEREOF, the partners have executed this Agreement as of the date first above written.

Partner 1: _____________________
Date: _____________________

Partner 2: _____________________
Date: _____________________";
    }

    private function getRentalContent(): string
    {
        return "RENTAL AGREEMENT

This Rental Agreement (the 'Agreement') is entered into on [lease_start] between:

[landlord_name] (the 'Landlord')
and
[tenant_name] (the 'Tenant')

1. PROPERTY
The Landlord hereby rents to the Tenant the property located at: [property_address]

2. TERM
This Agreement shall commence on [lease_start] and continue until [lease_end].

3. RENT
The Tenant shall pay monthly rent of [rent_amount] due on the first day of each month.

4. SECURITY DEPOSIT
The Tenant shall pay a security deposit of [security_deposit] upon execution of this Agreement.

5. USE OF PROPERTY
The Tenant shall use the property solely for residential purposes and shall not sublet without written permission.

6. TERMINATION
Either party may terminate this Agreement with 30 days written notice.

IN WITNESS WHEREOF, the parties have executed this Agreement as of the date first above written.

Landlord: _____________________
Date: _____________________

Tenant: _____________________
Date: _____________________";
    }
}
