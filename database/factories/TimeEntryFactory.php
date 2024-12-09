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
        // Generierung einer Startzeit zwischen 07:00 und 09:00 Uhr
        $startHour = $this->faker->numberBetween(7, 9); // Zufällige Stunde zwischen 7 und 9
        $startMinute = $this->faker->numberBetween(0, 59); // Zufällige Minuten
        $startTime = sprintf('%02d:%02d', $startHour, $startMinute); // Format HH:mm

        // Endzeit genau 8 Stunden nach der Startzeit
        $endHour = $startHour + 8; // Arbeitsdauer 8 Stunden
        $endMinute = $this->faker->numberBetween(0, 59);
        $endTime = sprintf('%02d:%02d', $endHour, $endMinute);

        // Zufällige Pausenzeit
        $breakDuration = $this->faker->numberBetween(30, 60); // Pausenzeit zwischen 30 und 60 Minuten

        // Zufälliges Datum in den letzten 14 Tagen
        $date = $this->faker->dateTimeBetween('-14 days', 'now')->format('Y-m-d');

        return [
            'employee_id' => Employee::inRandomOrder()->first()->id ?? Employee::factory()->create()->id,
            'date' => $date,
            'time_start' => $startTime,
            'time_end' => $endTime,
            'break_duration' => $breakDuration,
            'activity_type' => $this->faker->randomElement(['productive', 'non-productive']),
        ];
    }
}
