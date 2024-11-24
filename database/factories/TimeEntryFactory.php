<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Employee;
use App\Models\TimeEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimeEntryFactory extends Factory
{
    protected $model = TimeEntry::class;

    public function definition(): array
    {
        $start = $this->faker->time('H:i');
        $end = $this->faker->time('H:i', strtotime('+8 hours', strtotime($start)));
        $breakDuration = $this->faker->numberBetween(0, 60); // Pausenzeit in Minuten

        return [
            'employee_id' => Employee::factory(), // Erzeugt automatisch einen neuen Mitarbeiter
            'date' => $this->faker->date(),
            'time_start' => $start,
            'time_end' => $end,
            'break_duration' => $breakDuration,
            'activity_type' => $this->faker->randomElement(['productive', 'non-productive']),
        ];
    }
}
