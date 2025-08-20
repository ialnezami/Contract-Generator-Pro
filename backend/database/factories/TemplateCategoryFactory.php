<?php

namespace Database\Factories;

use App\Models\TemplateCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TemplateCategory>
 */
class TemplateCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        
        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'icon' => fake()->randomElement([
                'fas fa-file-contract',
                'fas fa-users',
                'fas fa-briefcase',
                'fas fa-home',
                'fas fa-tools',
                'fas fa-chart-line',
                'fas fa-laptop-code',
                'fas fa-dollar-sign',
                'fas fa-heartbeat',
                'fas fa-graduation-cap',
                'fas fa-balance-scale',
                'fas fa-palette',
                'fas fa-truck',
                'fas fa-industry',
                'fas fa-lightbulb',
            ]),
            'color' => fake()->randomElement([
                '#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6',
                '#1abc9c', '#27ae60', '#e67e22', '#8e44ad', '#34495e',
                '#e91e63', '#795548', '#607d8b', '#ff9800', '#95a5a6',
            ]),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the category is inactive
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the category is for business contracts
     */
    public function business(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Business & Commercial',
            'slug' => 'business-commercial',
            'description' => 'Business agreements, partnerships, and commercial contracts',
            'icon' => 'fas fa-briefcase',
            'color' => '#2ecc71',
        ]);
    }

    /**
     * Indicate that the category is for employment contracts
     */
    public function employment(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Employment & HR',
            'slug' => 'employment-hr',
            'description' => 'Employment contracts, non-disclosure agreements, and HR-related documents',
            'icon' => 'fas fa-users',
            'color' => '#3498db',
        ]);
    }

    /**
     * Indicate that the category is for real estate contracts
     */
    public function realEstate(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Real Estate',
            'slug' => 'real-estate',
            'description' => 'Lease agreements, property contracts, and real estate documents',
            'icon' => 'fas fa-home',
            'color' => '#e74c3c',
        ]);
    }

    /**
     * Indicate that the category is for service agreements
     */
    public function service(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Service Agreements',
            'slug' => 'service-agreements',
            'description' => 'Service contracts, consulting agreements, and professional services',
            'icon' => 'fas fa-tools',
            'color' => '#f39c12',
        ]);
    }

    /**
     * Indicate that the category is for technology contracts
     */
    public function technology(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Technology & IT',
            'slug' => 'technology-it',
            'description' => 'Software licenses, IT services, and technology agreements',
            'icon' => 'fas fa-laptop-code',
            'color' => '#1abc9c',
        ]);
    }
}
