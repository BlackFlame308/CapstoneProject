<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            ['name' => 'add_households', 'description' => 'Can add new households'],
            ['name' => 'update_households', 'description' => 'Can update household information'],
            ['name' => 'delete_households', 'description' => 'Can delete households'],
            ['name' => 'manage_users', 'description' => 'Can manage users'],
            ['name' => 'manage_responders', 'description' => 'Can manage responders'],
            ['name' => 'manage_evacuation_officers', 'description' => 'Can manage evacuation officers'],
            ['name' => 'view_analytics', 'description' => 'Can view analytics'],
            ['name' => 'create_disaster_events', 'description' => 'Can create disaster events'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], ['description' => $permission['description'], 'guard_name' => 'web']);
        }

        // Create Roles
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'Super Admin'],
            ['description' => 'Full system access - Barangay Captain', 'guard_name' => 'web']
        );

        $encoderRole = Role::firstOrCreate(
            ['name' => 'Encoder'],
            ['description' => 'Can manage households and users', 'guard_name' => 'web']
        );

        $viewerRole = Role::firstOrCreate(
            ['name' => 'Viewer'],
            ['description' => 'Can only view data', 'guard_name' => 'web']
        );

        // Assign permissions to roles
        $allPermissions = Permission::all();
        $superAdminRole->permissions()->sync($allPermissions->pluck('id'));

        // Encoder permissions
        $encoderPermissions = Permission::whereIn('name', [
            'add_households',
            'update_households',
            'manage_users',
            'view_analytics'
        ])->pluck('id');
        $encoderRole->permissions()->sync($encoderPermissions);

        // Viewer permissions
        $viewerPermissions = Permission::whereIn('name', [
            'view_analytics'
        ])->pluck('id');
        $viewerRole->permissions()->sync($viewerPermissions);
    }
}