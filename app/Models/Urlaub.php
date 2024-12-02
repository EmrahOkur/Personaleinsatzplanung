<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Urlaub extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable =
        [
            'employee_id',
            'datum_start',
            'datum_ende',
            'status',
            'selectedDates',
        ];

}
