<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // all ids from addresses as Collection
        $addressIds = Address::pluck('id');

        // Beispiel-Benutzer
        User::factory()->create([
            'name' => 'Admin',
            'vorname' => 'Ein',
            'role' => 'admin',
            'email' => 'adm@adm.de',
            'password' => '$2y$10$dzo5Jfx3cpCMlPx0DNv65OcRBjW.hvFKnyRtOLoBB0QSbvjEC/IgS', // admin123 https://onlinephp.io/password-hash
        ]);

        $employee = Employee::factory()
            ->create([
                'first_name' => 'Ein',
                'last_name' => 'Mitarbeiter',
                'department_id' => 1,
                'address_id' => $addressIds->random(),
            ]);
        User::factory()->create([
            'name' => 'Mitarbeiter',
            'vorname' => 'Ein',
            'role' => 'employee',
            'email' => 'mit@mit.de',
            'password' => '$2y$10$wCooawvFCkK/UjjF0Yh9Juou46lv.C5ZZUdBXRffY3zDnhf6fpFva', // mit123 https://onlinephp.io/password-hash
            'employee_id' => $employee->id,
        ]);
        $employee = Employee::factory()
            ->create([
                'first_name' => 'Ein',
                'last_name' => 'Manager',
                'department_id' => 1,
                'address_id' => $addressIds->random(),
            ]);
        User::factory()->create([
            'name' => 'Manager',
            'vorname' => 'Ein',
            'role' => 'manager',
            'email' => 'man@man.de',
            'password' => '$2y$10$6AfI3UIQXQwxHAKu22OLiOiD4eMxrarVFhRlDkGnE5KPWKfJsRlci', // man123 https://onlinephp.io/password-hash
            'employee_id' => $employee->id,
        ]);
    }
}
