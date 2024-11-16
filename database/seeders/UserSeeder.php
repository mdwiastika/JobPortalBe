<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::create([
            'name' => 'super_admin',
        ]);
        $adminRole = Role::create([
            'name' => 'admin',
        ]);
        $jobSeekerRole = Role::create([
            'name' => 'job_seeker',
        ]);
        $recruiterRole = Role::create([
            'name' => 'recruiter',
        ]);
        User::factory()->count(10)->withRole('super_admin')->create();
        User::factory()->count(10)->withRole('admin')->create();
        User::factory()->count(10)->withRole('job_seeker')->create();
        User::factory()->count(10)->withRole('recruiter')->create();
    }
}
