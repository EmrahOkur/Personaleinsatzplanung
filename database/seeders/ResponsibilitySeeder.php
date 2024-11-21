<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\ResponsibilityModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResponsibilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = Department::all();
        $employees = Employee::all();
        $i = 0;
        foreach ($departments as $d) {
            DB::table('responsibilities')->insert([
                'employee_id' => $employees[$i]->id,
                'department_id' => $d->id,
            ]);
            $i++;
            DB::table('responsibilities')->insert([
                'employee_id' => $employees[$i]->id,
                'department_id' => $d->id,
            ]);
            $i++;
        }
    }
}
