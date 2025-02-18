<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Company::create([
            'name' => 'Test',
        ]);

        User::create([
            'company_id' => 1,
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => Hash::make('1234'),
            'role' => 'engineer',
        ]);

        Project::create([
            'name' => 'P1',
            'company_id' => 1,
            'referent_id' => 1,
            'address' => 'Test Address',
            'is_mask_valided' => true,
            'is_mask_distributed' => true,
            'comment' => 'Test Comment',
        ]);

        Project::create([
            'name' => 'P2',
            'company_id' => 1,
            'referent_id' => 1,
            'address' => 'Test Address',
            'is_mask_valided' => false,
            'is_mask_distributed' => false,
            'comment' => 'Test Comment',
        ]);

        ProjectUser::create([
            'project_id' => 1,
            'user_id' => 1,
        ]);

        ProjectUser::create([
            'project_id' => 2,
            'user_id' => 1,
        ]);
    }
}
