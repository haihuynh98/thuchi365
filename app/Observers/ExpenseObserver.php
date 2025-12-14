<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ExpenseObserver
{
    public function creating(Expense $expense): void
    {
        if (Auth::check()) {
            $expense->created_by = Auth::id();
            $expense->updated_by = Auth::id();
        }
    }

    public function created(Expense $expense): void
    {
        $this->logAudit($expense, 'created', null, $expense->getAttributes());
    }

    public function updating(Expense $expense): void
    {
        if (Auth::check()) {
            $expense->updated_by = Auth::id();
        }
    }

    public function updated(Expense $expense): void
    {
        $this->logAudit($expense, 'updated', $expense->getOriginal(), $expense->getChanges());
    }

    public function deleted(Expense $expense): void
    {
        $this->logAudit($expense, 'deleted', $expense->getAttributes(), null);
    }

    protected function logAudit(Expense $expense, string $action, ?array $oldValues, ?array $newValues): void
    {
        AuditLog::create([
            'auditable_type' => Expense::class,
            'auditable_id' => $expense->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
