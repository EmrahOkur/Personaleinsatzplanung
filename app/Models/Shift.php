<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    use HasFactory;
    protected $fillable = ['date_shift','start_time','end_time','user_id'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
