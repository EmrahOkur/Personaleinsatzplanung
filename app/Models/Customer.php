<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['vorname', 'nachname', 'address_id'];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function getFullAddressAttribute()
    {
        return $this->address->fullAddress();
    }
}
