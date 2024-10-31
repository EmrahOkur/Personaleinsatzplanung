<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::factory()->createMany([
            [
                'name' => 'Lager',
                'short_name' => 'Lg',
            ],
            [
                'name' => 'Verkauf',
                'short_name' => 'Verk',
            ],
            [
                'name' => 'Produktion 1',
                'short_name' => 'Prod1',
            ],
            [
                'name' => 'Produktion 2',
                'short_name' => 'Prod2',
            ],
            [
                'name' => 'Produktion 3',
                'short_name' => 'Pro3',
            ],
            [
                'name' => 'Rechnungswesen',
                'short_name' => 'Rewe',
            ],
        ]);
    }
}
