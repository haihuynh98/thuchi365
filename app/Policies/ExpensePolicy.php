<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('ViewAny:Expense') || $user->hasRole('admin');
    }

    public function view(User $user, Expense $expense): bool
    {
        return $user->hasPermissionTo('View:Expense') || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Create:Expense') || $user->hasRole('admin');
    }

    public function update(User $user, Expense $expense): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if (!$user->hasPermissionTo('Update:Expense')) {
            return false;
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
        
        if (!$user->hasPermissionTo('Delete:Expense')) {
            return false;
        }
        
        if ($user->hasRole('manager')) {
            // Manager chỉ xóa được trong ngày hiện tại
            return $expense->recorded_at->isToday() && !$expense->is_locked;
        }
        
        return false;
    }
}
