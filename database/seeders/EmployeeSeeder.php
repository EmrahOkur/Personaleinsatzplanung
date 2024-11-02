<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect(range(1, 50))->each(function () {
            Employee::factory()
                ->has(Address::factory())
                ->create([
                    'department_id' => Department::inRandomOrder()->first()->id,
                ]);
        });
    }
}
