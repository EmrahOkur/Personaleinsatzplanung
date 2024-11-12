<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'time_start',
        'time_end',
        'break_duration',
        'activity_type',
    ];

    /**
     * Beziehung zum Employee-Model.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Accessor für die berechnete Nettoarbeitszeit in Stunden.
     */
    public function getNetWorkHoursAttribute(): float
    {
        $start = strtotime($this->time_start);
        $end = strtotime($this->time_end);
        $break = $this->break_duration * 60; // Annahme: break_duration ist in Minuten

        return max(0, ($end - $start - $break) / 3600); // Sicherstellen, dass die Zeit nicht negativ ist
    }

    /**
     * Scope zur Filterung der Zeiteinträge nach Datum.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDate($query, string $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope zur Filterung der Zeiteinträge nach Mitarbeiter.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }
}
