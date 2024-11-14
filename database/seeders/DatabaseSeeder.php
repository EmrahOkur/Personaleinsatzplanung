<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Beispiel-Benutzer
        User::factory()->create([
            'name' => 'Wendt',
            'vorname' => 'Patrik',
            'role' => 'manager',
            'email' => 'pwe@pwe.de',
            'password' => '$2y$10$Ub.ZAwHgIer4/r0h6TbAmuCVQSnTX81eUh72q17BQW9dxn8YMjWcK', // pwe123 https://onlinephp.io/password-hash
        ]);

        User::factory()->create([
            'name' => 'Albig',
            'vorname' => 'Malte',
            'role' => 'manager',
            'email' => 'alm@alm.de',
            'password' => '$2y$10$9FCLItMjbuvEgsV745Pxwurm7cx4ldGOJjELTwnirdCPp013y/9dW', // https://onlinephp.io/password-hash
        ]);
        User::factory()->create([
            'name' => 'Admin',
            'vorname' => 'Admin',
            'role' => 'admin',
            'email' => 'admin@admin.de',
            'password' => '$2y$10$rO1u4wYJRgkbGyzNkHUvt.HIZGcvGorob43XeHBWGCJyfC68Qygou', // admin123 https://onlinephp.io/password-hash
        ]);

        // Andere Seeders aufrufen
        $this->call([
            DepartmentSeeder::class,
            EmployeeSeeder::class,
            DepartmentHeadSeeder::class,
            TimeEntrySeeder::class, // TimeEntrySeeder hinzugefügt
        ]);
    }
}
