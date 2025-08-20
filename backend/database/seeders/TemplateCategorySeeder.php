<?php

namespace Database\Seeders;

use App\Models\TemplateCategory;
use Illuminate\Database\Seeder;

class TemplateCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Employment & HR',
                'description' => 'Employment contracts, non-disclosure agreements, and HR-related documents',
                'icon' => 'fas fa-users',
                'color' => '#3498db',
                'sort_order' => 1,
            ],
            [
                'name' => 'Business & Commercial',
                'description' => 'Business agreements, partnerships, and commercial contracts',
                'icon' => 'fas fa-briefcase',
                'color' => '#2ecc71',
                'sort_order' => 2,
            ],
            [
                'name' => 'Real Estate',
                'description' => 'Lease agreements, property contracts, and real estate documents',
                'icon' => 'fas fa-home',
                'color' => '#e74c3c',
                'sort_order' => 3,
            ],
            [
                'name' => 'Service Agreements',
                'description' => 'Service contracts, consulting agreements, and professional services',
                'icon' => 'fas fa-tools',
                'color' => '#f39c12',
                'sort_order' => 4,
            ],
            [
                'name' => 'Sales & Marketing',
                'description' => 'Sales contracts, marketing agreements, and distribution contracts',
                'icon' => 'fas fa-chart-line',
                'color' => '#9b59b6',
                'sort_order' => 5,
            ],
            [
                'name' => 'Technology & IT',
                'description' => 'Software licenses, IT services, and technology agreements',
                'icon' => 'fas fa-laptop-code',
                'color' => '#1abc9c',
                'sort_order' => 6,
            ],
            [
                'name' => 'Financial & Investment',
                'description' => 'Investment agreements, loan contracts, and financial services',
                'icon' => 'fas fa-dollar-sign',
                'color' => '#27ae60',
                'sort_order' => 7,
            ],
            [
                'name' => 'Healthcare & Medical',
                'description' => 'Medical service agreements, patient contracts, and healthcare services',
                'icon' => 'fas fa-heartbeat',
                'color' => '#e67e22',
                'sort_order' => 8,
            ],
            [
                'name' => 'Education & Training',
                'description' => 'Training agreements, educational services, and course contracts',
                'icon' => 'fas fa-graduation-cap',
                'color' => '#8e44ad',
                'sort_order' => 9,
            ],
            [
                'name' => 'Legal & Compliance',
                'description' => 'Legal agreements, compliance documents, and regulatory contracts',
                'icon' => 'fas fa-balance-scale',
                'color' => '#34495e',
                'sort_order' => 10,
            ],
            [
                'name' => 'Creative & Media',
                'description' => 'Content creation, media production, and creative service contracts',
                'icon' => 'fas fa-palette',
                'color' => '#e91e63',
                'sort_order' => 11,
            ],
            [
                'name' => 'Transportation & Logistics',
                'description' => 'Shipping contracts, transportation services, and logistics agreements',
                'icon' => 'fas fa-truck',
                'color' => '#795548',
                'sort_order' => 12,
            ],
            [
                'name' => 'Manufacturing & Supply',
                'description' => 'Manufacturing agreements, supply contracts, and production services',
                'icon' => 'fas fa-industry',
                'color' => '#607d8b',
                'sort_order' => 13,
            ],
            [
                'name' => 'Consulting & Advisory',
                'description' => 'Consulting agreements, advisory services, and expert contracts',
                'icon' => 'fas fa-lightbulb',
                'color' => '#ff9800',
                'sort_order' => 14,
            ],
            [
                'name' => 'General & Miscellaneous',
                'description' => 'General contracts, miscellaneous agreements, and custom documents',
                'icon' => 'fas fa-file-contract',
                'color' => '#95a5a6',
                'sort_order' => 15,
            ],
        ];

        foreach ($categories as $category) {
            TemplateCategory::create($category);
        }
    }
}
