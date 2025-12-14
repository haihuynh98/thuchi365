<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'employee_id',
        'name',
        'phone',
        'status',
        'note',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
