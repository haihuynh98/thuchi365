<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailySummary extends Model
{
    protected $fillable = [
        'date',
        'total_income',
        'total_expense',
        'total_revenue',
        'breakdown_by_employee',
        'is_closed',
    ];

    protected $casts = [
        'date' => 'date',
        'total_income' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'breakdown_by_employee' => 'array',
        'is_closed' => 'boolean',
    ];
}
