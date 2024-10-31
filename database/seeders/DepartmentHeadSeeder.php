<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class DepartmentHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = Department::all();

        foreach ($departments as $department) {
            $randomEmployee = Employee::where('department_id', $department->id)
                ->inRandomOrder()
                ->first();

            if ($randomEmployee) {
                $department->update([
                    'department_head_id' => $randomEmployee->id,
                ]);

                //$this->command->info("Department {$department->name} head assigned: {$randomEmployee->name}");
            } else {
                $this->command->warn("No eligible employees found for department: {$department->name}");
            }
        }
    }
}
