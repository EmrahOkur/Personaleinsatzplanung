<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Responsibility extends Model
{
    protected $fillable = [
        'employee_id',
        'department_id',
    ];

    public function employee() {
        return $this->belongsTo(Employee::class);
    }
}
