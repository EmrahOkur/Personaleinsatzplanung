<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Services\AddressService;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(AddressService $addressService): void
    {
        $addresses = $addressService->getAll();

        collect(range(1, 50))->each(function () {
            $employee = Employee::factory()
                ->create([
                    'department_id' => Department::where('id', '<', 7)->inRandomOrder()->first()->id,
                    'address_id' => Address::inRandomOrder()->first()->id,
                ]);
            User::factory()->create([
                'vorname' => $employee->first_name,
                'name' => $employee->last_name,
                'email' => $employee->email,
                'role' => 'employee',
                'employee_id' => $employee->id,
            ]);
        });

        $externDepartment = Department::factory()->create(
            [
                'name' => 'Extern',
                'short_name' => 'Ex',
            ]);

        collect(range(1, 5))->each(function () use ($externDepartment) {
            $employee = Employee::factory()
                ->create([
                    'department_id' => $externDepartment->id,
                    'address_id' => Address::inRandomOrder()->first()->id,
                ]);

            User::factory()->create([
                'vorname' => $employee->first_name,
                'name' => $employee->last_name,
                'email' => $employee->email,
                'role' => 'employee',
                'employee_id' => $employee->id,
            ]);
        });
    }
}
