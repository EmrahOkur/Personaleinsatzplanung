<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'employee_id',
        'street',
        'house_number',
        'additional_info',
        'zip_code',
        'city',
        'state',
        'country',
    ];

    public function getFullAddressAttribute()
    {
        return "{$this->street} {$this->house_number} {$this->zip_code} {$this->city}";
    }
}
