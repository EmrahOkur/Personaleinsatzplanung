<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Urlaub;
use Illuminate\Database\Seeder;

class UrlaubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::all();
        foreach ($employees as $e) {
            Urlaub::factory()->count(5)->create(
                ['employee_id' => $e->id, 'status' => 'accepted']
            );
        }
    }
}
