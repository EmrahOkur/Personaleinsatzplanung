<?php

declare(strict_types=1);

namespace Database\Factories;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departmentIds = Department::pluck('id')->toArray();

        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $email = $firstName . '.' . $lastName;
        $email = str_replace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $email);

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => strtolower($email) . '@personal.de',
            'phone' => $this->faker->phoneNumber(),
            'birth_date' => $this->faker->date(),
            'employee_number' => $this->faker->unique()->numerify('PNR####'),
            'hire_date' => $this->faker->date(),
            'position' => $this->faker->jobTitle(),
            'vacation_days' => 30,
            'status' => 'active',
            'department_id' => $departmentIds[array_rand($departmentIds)],
        ];
    }
}
