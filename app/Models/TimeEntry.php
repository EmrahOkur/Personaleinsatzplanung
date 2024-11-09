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

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
