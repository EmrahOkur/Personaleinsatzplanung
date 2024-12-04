<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'birth_date',
        'employee_number',
        'hire_date',
        'exit_date',
        'position',
        'vacation_days',
        'status',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $dates = [
        'birth_date',
        'hire_date',
        'exit_date',
    ];

    public function address()
    {
        return $this->hasOne(Address::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }

    public function responsibilities()
    {
        return $this->belongsToMany(Department::class, 'responsibilities');
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function shifts(): BelongsToMany
    {
        return $this->belongsToMany(Shift::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullNameAndDepartmentAttribute()
    {
        return "{$this->first_name} {$this->last_name}" . ($this->department ? " ({$this->department->name})" : '');
    }

    public static function getExternalEmployees()
    {
        $departmentId = Department::where('name', 'Extern')->first()->id;

        return self::where('department_id', $departmentId)->get()->toArray();
    }

    public static function getNextWeekAvailabilities()
    {
        // Get external employees
        $employees = self::getExternalEmployees();

        // Get next week's dates
        $nextWeek = collect();
        $startOfNextWeek = now()->addWeek()->startOfWeek();

        // Build array of next week's dates (Monday to Friday)
        for ($i = 0; $i < 5; $i++) {
            $date = $startOfNextWeek->copy()->addDays($i);
            $nextWeek->push([
                'date' => $date->format('Y-m-d'),
                'weekday' => $date->dayOfWeekIso,
            ]);
        }

        // Initialize structured array
        $structuredAvailabilities = [];

        // Get availabilities for each employee
        foreach ($employees as $employee) {
            $employeeAvailabilities = Availability::where('employee_id', $employee['id'])
                ->whereIn('weekday', range(1, 5))
                ->get();

            // Map availabilities to actual dates
            foreach ($nextWeek as $day) {
                $dayAvailability = $employeeAvailabilities
                    ->where('weekday', $day['weekday'])
                    ->first();

                if ($dayAvailability) {
                    $date = $day['date'];
                    $startHour = (int) $dayAvailability->start_time->format('H');
                    $endHour = (int) $dayAvailability->end_time->format('H');

                    // Initialize date in array if not exists
                    if (! isset($structuredAvailabilities[$date])) {
                        $structuredAvailabilities[$date] = [
                            'date' => $date,
                            'weekday' => $dayAvailability->weekday,
                            'weekday_name' => [
                                1 => 'Montag',
                                2 => 'Dienstag',
                                3 => 'Mittwoch',
                                4 => 'Donnerstag',
                                5 => 'Freitag',
                            ][$dayAvailability->weekday],
                            'hours' => [],
                        ];
                    }

                    // Add availability for each hour in the range
                    for ($hour = $startHour; $hour < $endHour; $hour++) {
                        if (! isset($structuredAvailabilities[$date]['hours'][$hour])) {
                            $structuredAvailabilities[$date]['hours'][$hour] = [];
                        }

                        $structuredAvailabilities[$date]['hours'][$hour][] = [
                            'employee_id' => $employee['id'],
                            'employee_name' => $employee['first_name'] . ' ' . $employee['last_name'],
                            'employee_number' => $employee['employee_number'],
                            'start_time' => sprintf('%02d:00', $hour),
                            'end_time' => sprintf('%02d:00', $hour + 1),
                        ];
                    }
                }
            }
        }

        // Sort by date and convert to array
        ksort($structuredAvailabilities);

        // Convert hours arrays to sorted arrays
        foreach ($structuredAvailabilities as &$day) {
            ksort($day['hours']);
            $day['hours'] = array_values($day['hours']);
        }

        return array_values($structuredAvailabilities);
    }
}
