<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Variante 1: Mit has()
        Employee::factory()
            ->count(10)
            ->has(Address::factory())
            ->create();
    }
}
