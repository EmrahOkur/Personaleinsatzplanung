<?php

declare(strict_types=1);
// app/Models/Availability.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    protected $fillable = [
        'employee_id',
        'weekday',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'weekday' => 'integer',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
