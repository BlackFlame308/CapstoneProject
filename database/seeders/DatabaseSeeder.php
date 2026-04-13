<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First, seed roles and permissions
        $this->call(RoleSeeder::class);

        // Fetch roles
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $encoderRole = Role::where('name', 'Encoder')->first();
        $viewerRole = Role::where('name', 'Viewer')->first();

        // Create a Super Admin (Captain) user
        $superAdmin = User::firstOrCreate(
            ['email' => 'captain@safetrack.local'],
            [
                'name' => 'Barangay Captain',
                'password' => bcrypt('password'),
                'role_id' => $superAdminRole->id,
            ]
        );

        // Create an Encoder user
        $encoder = User::firstOrCreate(
            ['email' => 'encoder@safetrack.local'],
            [
                'name' => 'Data Encoder',
                'password' => bcrypt('password'),
                'role_id' => $encoderRole->id,
            ]
        );

        // Create a Viewer user
        $viewer = User::firstOrCreate(
            ['email' => 'viewer@safetrack.local'],
            [
                'name' => 'System Viewer',
                'password' => bcrypt('password'),
                'role_id' => $viewerRole->id,
            ]
        );

        // Optional: Ensure roles are synced with permissions again just in case
        $superAdminRole->permissions()->sync(\App\Models\Permission::all()->pluck('id'));
        $encoderRole->permissions()->sync(\App\Models\Permission::whereIn('name', [
            'add_households',
            'update_households',
            'view_households',
            'manage_users',
            'view_analytics'
        ])->pluck('id'));
    }
}