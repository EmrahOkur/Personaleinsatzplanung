<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    /** @use HasFactory<\Database\Factories\DepartmentFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'short_name',
        'department_head_id',
    ];

    public function departmentHead()
    {
        return $this->belongsTo(Employee::class);
    }

    public function responsibleEmployees()
    {
        return $this->belongsToMany(Employee::class, 'responsibilities');
    }

    public function department()
    {
        return $this->belongsToMany(Shift::class);
    }
}
