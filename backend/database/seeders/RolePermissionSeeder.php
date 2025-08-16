<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User permissions
            'view_profile',
            'edit_profile',
            'change_password',
            
            // Contract permissions
            'view_contracts',
            'create_contracts',
            'edit_contracts',
            'delete_contracts',
            'sign_contracts',
            'generate_pdf',
            
            // Template permissions
            'view_templates',
            'create_templates',
            'edit_templates',
            'delete_templates',
            'clone_templates',
            'publish_templates',
            
            // Admin permissions
            'manage_users',
            'manage_roles',
            'view_statistics',
            'system_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $userRole = Role::create(['name' => 'user']);
        $premiumRole = Role::create(['name' => 'premium']);
        $adminRole = Role::create(['name' => 'admin']);

        // Assign permissions to user role
        $userRole->givePermissionTo([
            'view_profile',
            'edit_profile',
            'change_password',
            'view_contracts',
            'create_contracts',
            'edit_contracts',
            'delete_contracts',
            'sign_contracts',
            'generate_pdf',
            'view_templates',
            'create_templates',
            'edit_templates',
            'delete_templates',
            'clone_templates',
        ]);

        // Assign permissions to premium role
        $premiumRole->givePermissionTo([
            'view_profile',
            'edit_profile',
            'change_password',
            'view_contracts',
            'create_contracts',
            'edit_contracts',
            'delete_contracts',
            'sign_contracts',
            'generate_pdf',
            'view_templates',
            'create_templates',
            'edit_templates',
            'delete_templates',
            'clone_templates',
            'publish_templates',
        ]);

        // Assign all permissions to admin role
        $adminRole->givePermissionTo(Permission::all());
    }
}
