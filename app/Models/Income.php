<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    protected $fillable = [
        'employee_id',
        'revenue',
        'tip',
        'penalty',
        'facility',
        'note',
        'recorded_at',
        'is_locked',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'revenue' => 'decimal:2',
        'tip' => 'decimal:2',
        'penalty' => 'decimal:2',
        'facility' => 'decimal:2',
        'recorded_at' => 'date',
        'is_locked' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function getTotalAttribute(): float
    {
        $revenue = (float) $this->revenue;

        // Tổng thu = Doanh thu + 10% + Phạt + CSVC
        return ($revenue * 1.1) + (float) $this->penalty + (float) $this->facility;
    }
}
