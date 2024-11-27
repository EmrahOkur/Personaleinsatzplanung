<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'birth_date',
        'employee_number',
        'hire_date',
        'exit_date',
        'position',
        'vacation_days',
        'status',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $dates = [
        'birth_date',
        'hire_date',
        'exit_date',
    ];

    public function address()
    {
        return $this->hasOne(Address::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }

    public function responsibilities()
    {
        return $this->belongsToMany(Department::class, 'responsibilities');
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function shifts(): BelongsToMany
    {
        return $this->belongsToMany(Shift::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullNameAndDepartmentAttribute()
    {
        return "{$this->first_name} {$this->last_name}" . ($this->department ? " ({$this->department->name})" : '');
    }
}
