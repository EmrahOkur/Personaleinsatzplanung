<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    use HasFactory;

    /**
     * Definierte Felder, die massenbefüllbar sind.
     *
     * @var array
     */
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
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    /**
     * Accessor für die berechnete Nettoarbeitszeit in Stunden.
     */
    /**
     * Accessor für die berechnete Nettoarbeitszeit in Stunden.
     */
    public function getNetWorkHoursAttribute(): float
    {
        if (! $this->time_start || ! $this->time_end) {
            return 0.0; // Rückgabe von 0 Stunden bei fehlenden Daten
        }

        $start = strtotime($this->time_start);
        $end = strtotime($this->time_end);
        $break = ($this->break_duration ?? 0) * 60; // Pausenzeit in Sekunden

        // Arbeitszeit in Sekunden berechnen und negatives Ergebnis verhindern
        $workSeconds = max(0, $end - $start - $break);

        return round($workSeconds / 3600, 2); // Rückgabe in Stunden
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

    /**
     * Scope zur Filterung der Zeiteinträge für eine Rolle.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User                                  $user
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForRole($query, $user)
    {
        if ($user->isEmployee()) {
            return $query->where('employee_id', $user->employee_id);
        } elseif ($user->isManager()) {
            return $query; // Manager sehen alle Einträge
        } elseif ($user->isAdmin()) {
            return $query; // Admins sehen alle Einträge
        }

        return $query->whereRaw('1 = 0'); // Standardmäßig keine Einträge zurückgeben
    }

    /**
     * Scope zur Filterung nach einem bestimmten Zeitraum.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Berechnung der Gesamtstunden für einen Mitarbeiter in einem Zeitraum.
     */
    public static function calculateTotalHoursForEmployee(int $employeeId, string $startDate, string $endDate): float
    {
        return self::forEmployee($employeeId)
            ->forDateRange($startDate, $endDate)
            ->get()
            ->sum('net_work_hours');
    }

    /**
     * Gruppierung der Zeiteinträge nach Datum.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGroupByDate($query)
    {
        return $query->selectRaw('date, SUM(TIMESTAMPDIFF(MINUTE, time_start, time_end)) as total_minutes')
            ->groupBy('date');
    }
}
