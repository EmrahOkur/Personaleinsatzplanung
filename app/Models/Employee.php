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
                'weekday' => $date->dayOfWeekIso, // 1 = Monday, 7 = Sunday
            ]);
        }

        // Get availabilities for each employee
        $availabilities = [];
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
                    $availabilities[] = [
                        'employee_id' => $employee['id'],
                        'employee_name' => $employee['first_name'] . ' ' . $employee['last_name'],
                        'employee_number' => $employee['employee_number'],
                        'date' => $day['date'],
                        'weekday' => $dayAvailability->weekday,
                        'weekday_name' => [
                            1 => 'Montag',
                            2 => 'Dienstag',
                            3 => 'Mittwoch',
                            4 => 'Donnerstag',
                            5 => 'Freitag',
                        ][$dayAvailability->weekday],
                        'start_time' => $dayAvailability->start_time->format('H:i'),
                        'end_time' => $dayAvailability->end_time->format('H:i'),
                        'hours' => $dayAvailability->start_time->diffInHours($dayAvailability->end_time),
                    ];
                }
            }
            // dd($availabilities);
        }

        return collect($availabilities)->sortBy(['date', 'start_time'])->values()->all();
    }
}
