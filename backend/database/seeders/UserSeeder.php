<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@contractgeneratorpro.com',
            'password' => Hash::make('password'),
            'company' => 'Contract Generator Pro',
            'phone' => '+1-555-0123',
            'address' => '123 Business St',
            'city' => 'New York',
            'state' => 'NY',
            'zip_code' => '10001',
            'country' => 'USA',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create premium user
        $premium = User::create([
            'name' => 'Premium User',
            'email' => 'premium@example.com',
            'password' => Hash::make('password'),
            'company' => 'Premium Business LLC',
            'phone' => '+1-555-0456',
            'address' => '456 Premium Ave',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'zip_code' => '90210',
            'country' => 'USA',
            'is_active' => true,
        ]);
        $premium->assignRole('premium');

        // Create regular user
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'company' => 'Regular Business Inc',
            'phone' => '+1-555-0789',
            'address' => '789 Regular Blvd',
            'city' => 'Chicago',
            'state' => 'IL',
            'zip_code' => '60601',
            'country' => 'USA',
            'is_active' => true,
        ]);
        $user->assignRole('user');

        // Create demo user
        $demo = User::create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
            'company' => 'Demo Company',
            'phone' => '+1-555-0321',
            'address' => '321 Demo Way',
            'city' => 'Miami',
            'state' => 'FL',
            'zip_code' => '33101',
            'country' => 'USA',
            'is_active' => true,
        ]);
        $demo->assignRole('user');
    }
}
