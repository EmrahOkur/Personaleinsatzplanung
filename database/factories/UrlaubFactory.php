<?php

declare(strict_types=1);

namespace Database\Factories;

use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class UrlaubFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get current year's start and end
        $yearStart = new DateTime(date('Y-01-01'));
        $yearEnd = new DateTime(date('Y-12-31'));

        // Generate random start date (only weekdays)
        do {
            $startDate = $this->faker->dateTimeBetween($yearStart, $yearEnd);
            $weekday = (int) $startDate->format('N'); // 1 (Monday) to 7 (Sunday)
        } while ($weekday > 5); // Repeat if it's Saturday (6) or Sunday (7)

        return [
            'datum' => $startDate->format('Y-m-d'),
            'status' => $this->faker->randomElement(['pending']),
        ];
    }

    /**
     * Indicate that the vacation is approved.
     */
    public function approved(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
            ];
        });
    }

    /**
     * Indicate that the vacation is pending.
     */
    public function pending(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }
}
