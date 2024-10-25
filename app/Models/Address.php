<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /** @use HasFactory<\Database\Factories\AddressFactory> */
    use HasFactory;
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

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Vollständige Adresse als Accessor
    public function getFullAddressAttribute()
    {
        return "{$this->street} {$this->house_number}, {$this->zip_code} {$this->city}";
    }
}
