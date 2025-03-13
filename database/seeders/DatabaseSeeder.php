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
            'name' => 'Becip',
        ]);

        User::create([
            'company_id' => 1,
            'name' => 'Ingénieur Principal',
            'email' => 'celestin@honvault.com',
            'password' => Hash::make('azertyui'),
            'role' => 'engineer',
        ]);

    }
}
