<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
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
        'working_hours',
        'department_id',
    ];

    protected $dates = [
        'birth_date',
        'hire_date',
        'exit_date',
    ];

    public function address()
    {
        return $this->belongsTo(Address::class);
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

    private static function isTimeSlotAvailable($employeeId, $date, $startTime, $allAppointments)
    {
        // Endzeit der vollen Stunde berechnen
        $endTime = Carbon::parse($startTime)->addHour();

        // Filtere alle Termine, die mit dem Zeitfenster kollidieren
        $conflictingAppointments = $allAppointments->filter(function ($appointment) use ($employeeId, $date, $startTime, $endTime) {
            $appointmentDate = Carbon::parse($appointment->appointment_date)->format('Y-m-d');
            $appointmentStart = Carbon::parse($appointment->appointment_time);
            $appointmentEnd = $appointmentStart->copy()->addHour(); // Beispiel: 1 Stunde pro Termin

            return $appointment->employee_id == $employeeId
                && $appointmentDate === $date
                && $appointmentStart->lt($endTime) // Termin startet vor Endzeit
                && $appointmentEnd->gt($startTime); // Termin endet nach Startzeit
        });

        // Verfügbarkeit ist gegeben, wenn keine Konflikte existieren
        return $conflictingAppointments->isEmpty();
    }

    public static function getNextWeekAvailabilities()
    {
        // Get external employees
        $employees = self::getExternalEmployees();
        $nextWeek = collect();
        $startOfNextWeek = now()->addWeek()->startOfWeek();

        // Build next week dates
        for ($i = 0; $i < 5; $i++) {
            $date = $startOfNextWeek->copy()->addDays($i);
            $nextWeek->push([
                'date' => $date->format('Y-m-d'),
                'weekday' => $date->dayOfWeekIso,
            ]);
        }

        $structuredAvailabilities = [];

        // Get all appointments for next week up front
        $allAppointments = Orders::whereBetween('appointment_date', [
            $startOfNextWeek->format('Y-m-d'),
            $startOfNextWeek->copy()->addDays(4)->format('Y-m-d'),
        ])->get();

        // Get all vacation days for next week
        $vacationDays = Urlaub::whereBetween('datum', [
            $startOfNextWeek->format('Y-m-d'),
            $startOfNextWeek->copy()->addDays(4)->format('Y-m-d'),
        ])
            ->where('status', 'accepted') // Assuming we only consider approved vacation days
            ->get();

        foreach ($employees as $employee) {
            // Load the employee with their address relation
            $employeeWithAddress = self::with('address')->find($employee['id']);
            $employeeAvailabilities = Availability::where('employee_id', $employee['id'])
                ->whereIn('weekday', range(1, 5))
                ->get();

            foreach ($nextWeek as $day) {
                // Check if employee has vacation on this day
                $hasVacation = $vacationDays
                    ->where('employee_id', $employee['id'])
                    ->where('datum', $day['date'])
                    ->isNotEmpty();

                // Skip this day if employee is on vacation
                if ($hasVacation) {
                    continue;
                }

                $dayAvailability = $employeeAvailabilities
                    ->where('weekday', $day['weekday'])
                    ->first();

                if ($dayAvailability) {
                    $date = $day['date'];
                    $startHour = (int) $dayAvailability->start_time->format('H');
                    $endHour = (int) $dayAvailability->end_time->format('H');

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

                    for ($hour = $startHour; $hour < $endHour; $hour++) {
                        $timeSlot = sprintf('%02d:00', $hour);

                        // Check availability using the existing function
                        $isAvailable = self::isTimeSlotAvailable(
                            $employee['id'],
                            $date,
                            $timeSlot,
                            $allAppointments
                        );

                        if ($isAvailable) {
                            if (! isset($structuredAvailabilities[$date]['hours'][$hour])) {
                                $structuredAvailabilities[$date]['hours'][$hour] = [];
                            }

                            $structuredAvailabilities[$date]['hours'][$hour][] = [
                                'employee_id' => $employee['id'],
                                'employee_name' => $employee['first_name'] . ' ' . $employee['last_name'],
                                'employee_number' => $employee['employee_number'],
                                'full_address' => $employeeWithAddress->address ? $employeeWithAddress->address->full_address : null,
                                'start_time' => $timeSlot,
                                'end_time' => sprintf('%02d:00', $hour + 1),
                                'max_end_time' => $dayAvailability->end_time->format('H:i'),
                            ];
                        }
                    }
                }
            }
        }

        // Sort the results
        ksort($structuredAvailabilities);
        foreach ($structuredAvailabilities as &$day) {
            ksort($day['hours']);
            $day['hours'] = array_values($day['hours']);
        }

        return array_values($structuredAvailabilities);
    }
}
