<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First, seed roles and permissions
        $this->call(RoleSeeder::class);

        // Create a Super Admin (Captain) user
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        User::firstOrCreate(
            ['email' => 'captain@safetrack.local'],
            [
                'name' => 'Barangay Captain',
                'password' => bcrypt('password'),
                'role_id' => $superAdminRole->id,
            ]
        );

        // Create an Encoder user
        $encoderRole = Role::where('name', 'Encoder')->first();
        User::firstOrCreate(
            ['email' => 'encoder@safetrack.local'],
            [
                'name' => 'Data Encoder',
                'password' => bcrypt('password'),
                'role_id' => $encoderRole->id,
            ]
        );

        // Create a Viewer user
        $viewerRole = Role::where('name', 'Viewer')->first();
        User::firstOrCreate(
            ['email' => 'viewer@safetrack.local'],
            [
                'name' => 'System Viewer',
                'password' => bcrypt('password'),
                'role_id' => $viewerRole->id,
            ]
        );
    }
}
