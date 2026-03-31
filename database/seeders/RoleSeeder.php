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
        $captainRole = Role::firstOrCreate(
            ['name' => 'Captain'],
            ['description' => 'Barangay Captain - super admin privileges', 'guard_name' => 'web']
        );

        $encoderRole = Role::firstOrCreate(
            ['name' => 'Encoder'],
            ['description' => 'Can manage households and users', 'guard_name' => 'web']
        );

        $responderRole = Role::firstOrCreate(
            ['name' => 'Responder'],
            ['description' => 'Disaster responder role', 'guard_name' => 'web']
        );

        $evacuationOfficerRole = Role::firstOrCreate(
            ['name' => 'Evacuation Officer'],
            ['description' => 'Evacuation officer role', 'guard_name' => 'web']
        );

        $viewerRole = Role::firstOrCreate(
            ['name' => 'Viewer'],
            ['description' => 'Can only view data', 'guard_name' => 'web']
        );

        $householdRole = Role::firstOrCreate(
            ['name' => 'Household'],
            ['description' => 'Household account with limited access to own profile', 'guard_name' => 'web']
        );

        // Super admin alias support for old roles
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'Super Admin'],
            ['description' => 'Legacy Super Admin alias', 'guard_name' => 'web']
        );

        // Assign permissions to roles
        $allPermissions = Permission::all();
        $captainRole->permissions()->sync($allPermissions->pluck('id'));
        $superAdminRole->permissions()->sync($allPermissions->pluck('id')); // Legacy alias

        // Encoder permissions
        $encoderPermissions = Permission::whereIn('name', [
            'add_households',
            'update_households',
            'manage_users',
            'view_analytics'
        ])->pluck('id');
        $encoderRole->permissions()->sync($encoderPermissions);

        // Responder permissions (limited)
        $responderPermissions = Permission::whereIn('name', [
            'view_analytics'
        ])->pluck('id');
        $responderRole->permissions()->sync($responderPermissions);

        // Evacuation Officer permissions (limited)
        $evacuationOfficerPermissions = Permission::whereIn('name', [
            'view_analytics'
        ])->pluck('id');
        $evacuationOfficerRole->permissions()->sync($evacuationOfficerPermissions);

        // Viewer permissions
        $viewerPermissions = Permission::whereIn('name', [
            'view_analytics'
        ])->pluck('id');
        $viewerRole->permissions()->sync($viewerPermissions);

        // Household permissions (read-only by default)
        $householdPermissions = Permission::whereIn('name', [
            'view_analytics'
        ])->pluck('id');
        $householdRole->permissions()->sync($householdPermissions);
    }
}