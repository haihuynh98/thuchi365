<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function view(User $user, Expense $expense): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function update(User $user, Expense $expense): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if ($user->hasRole('manager')) {
            // Manager chỉ sửa được trong ngày hiện tại
            return $expense->recorded_at->isToday() && !$expense->is_locked;
        }
        
        return false;
    }

    public function delete(User $user, Expense $expense): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if ($user->hasRole('manager')) {
            // Manager chỉ xóa được trong ngày hiện tại
            return $expense->recorded_at->isToday() && !$expense->is_locked;
        }
        
        return false;
    }
}
