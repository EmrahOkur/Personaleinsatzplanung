<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'vorname',
        'email',
        'password',
        'employee_id',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function isEmployee()
    {
        return $this->employee_id !== null;
    }

    public function hasRole(string $role)
    {
        return $this->role === $role;
    }

    public function hasNotRole(string $role)
    {
        return $this->role !== $role;
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getFullName()
    {
        if ($this->isEmployee()) {
            return $this->employee->getFullNameAttribute();
        }

        return "{$this->vorname} {$this->name}";
    }
}
