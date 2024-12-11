<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Andere Seeders aufrufen
        $this->call([
            AddressSeeder::class,
            DepartmentSeeder::class,
            EmployeeSeeder::class,
            DepartmentHeadSeeder::class,
            TimeEntrySeeder::class, // TimeEntrySeeder hinzugefügt
            UserSeeder::class,
            ResponsibilitySeeder::class,
            UrlaubSeeder::class,
            CustomerSeeder::class,
            AvailabilitySeeder::class,
        ]);
    }
}
