<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Shift extends Model
{
    use HasFactory;
    protected $fillable = ['date_shift','start_time','end_time','amount_employees','shift_hours']; // 'user_id',
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class);
    }
}
