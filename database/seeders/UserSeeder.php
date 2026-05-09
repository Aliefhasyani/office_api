<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            
            $admin = User::create([
                'name'     => 'Administrator',
                'email'    => 'admin@company.com',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ]);

            $admin->employee()->create([
                'employee_name'   => 'Admin Utama',
                'employee_number' => 'ADM-001',
            ]);

            $employee1 = User::create([
                'name'     => 'Alief Hasyani',
                'email'    => 'alief@company.com',
                'password' => Hash::make('password123'),
                'role'     => 'employee',
            ]);

            $employee1->employee()->create([
                'employee_name'   => 'Mohammad Alief Hasyani',
                'employee_number' => 'EMP-001',
            ]);

            $employee2 = User::create([
                'name'     => 'Jane Doe',
                'email'    => 'jane@company.com',
                'password' => Hash::make('password123'),
                'role'     => 'employee',
            ]);

            $employee2->employee()->create([
                'employee_name'   => 'Jane Doe Professional',
                'employee_number' => 'EMP-002',
            ]);
        });
    }
}