<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AvailabilitySeeder extends Seeder
{
    public function run()
    {
        // Get external employees
        $externalEmployees = (new Employee)->getExternalEmployees();

        foreach ($externalEmployees as $employee) {
            // For each weekday (1 = Monday to 5 = Friday)
            for ($weekday = 1; $weekday <= 5; $weekday++) {
                // Generate random start hour between 8 and 16 (to ensure minimum 4 hours)
                $startHour = rand(8, 16);

                // Generate random shift duration between 4 and 9 hours
                $duration = rand(4, 9);

                // Calculate end hour (ensuring it doesn't exceed 20:00)
                $endHour = min($startHour + $duration, 20);

                // Create start and end times
                $startTime = Carbon::createFromTime($startHour, 0, 0)->format('H:i:s');
                $endTime = Carbon::createFromTime($endHour, 0, 0)->format('H:i:s');

                // Create availability record
                Availability::create([
                    'employee_id' => $employee['id'],
                    'weekday' => $weekday,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ]);
            }
        }
    }
}
