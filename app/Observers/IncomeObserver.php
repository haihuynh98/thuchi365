<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Income;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class IncomeObserver
{
    public function creating(Income $income): void
    {
        if (Auth::check()) {
            $income->created_by = Auth::id();
            $income->updated_by = Auth::id();
        }
    }

    public function created(Income $income): void
    {
        $this->logAudit($income, 'created', null, $income->getAttributes());
    }

    public function updating(Income $income): void
    {
        if (Auth::check()) {
            $income->updated_by = Auth::id();
        }
    }

    public function updated(Income $income): void
    {
        $this->logAudit($income, 'updated', $income->getOriginal(), $income->getChanges());
    }

    public function deleted(Income $income): void
    {
        $this->logAudit($income, 'deleted', $income->getAttributes(), null);
    }

    protected function logAudit(Income $income, string $action, ?array $oldValues, ?array $newValues): void
    {
        AuditLog::create([
            'auditable_type' => Income::class,
            'auditable_id' => $income->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
