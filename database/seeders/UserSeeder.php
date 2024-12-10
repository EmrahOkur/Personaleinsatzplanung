<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Department;
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
        $employee = Employee::factory()
            ->has(Address::factory())
            ->create([
                'first_name' => 'Patrik',
                'last_name' => 'Wendt',
                'department_id' => Department::inRandomOrder()->first()->id,
                'address_id' => $addressIds->random(),
            ]);
        $patrik = User::factory()->create([
            'name' => 'Wendt',
            'vorname' => 'Patrik',
            'role' => 'manager',
            'email' => 'pwe@pwe.de',
            'password' => '$2y$10$Ub.ZAwHgIer4/r0h6TbAmuCVQSnTX81eUh72q17BQW9dxn8YMjWcK', // pwe123 https://onlinephp.io/password-hash
            'employee_id' => $employee->id,
        ]);

        $employee = Employee::factory()
            ->has(Address::factory())
            ->create([
                'first_name' => 'Malte',
                'last_name' => 'Albig',
                'department_id' => Department::inRandomOrder()->first()->id,
                'address_id' => $addressIds->random(),
            ]);
        $malte = User::factory()->create([
            'name' => 'Albig',
            'vorname' => 'Malte',
            'role' => 'manager',
            'email' => 'alm@alm.de',
            'password' => '$2y$10$9FCLItMjbuvEgsV745Pxwurm7cx4ldGOJjELTwnirdCPp013y/9dW', // https://onlinephp.io/password-hash
            'employee_id' => $employee->id,
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'vorname' => 'Ein',
            'role' => 'admin',
            'email' => 'adm@adm.de',
            'password' => '$2y$10$dzo5Jfx3cpCMlPx0DNv65OcRBjW.hvFKnyRtOLoBB0QSbvjEC/IgS', // admin123 https://onlinephp.io/password-hash
        ]);

        $employee = Employee::factory()
            ->has(Address::factory())
            ->create([
                'first_name' => 'Ein',
                'last_name' => 'Mitarbeiter',
                'department_id' => Department::inRandomOrder()->first()->id,
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
            ->has(Address::factory())
            ->create([
                'first_name' => 'Ein',
                'last_name' => 'Manager',
                'department_id' => Department::inRandomOrder()->first()->id,
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
