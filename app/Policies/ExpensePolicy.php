<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        try {
            return $user->hasPermissionTo('ViewAny:Expense');
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            return false;
        }
    }

    public function view(User $user, Expense $expense): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        try {
            return $user->hasPermissionTo('View:Expense');
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            return false;
        }
    }

    public function create(User $user): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        try {
            return $user->hasPermissionTo('Create:Expense');
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            return false;
        }
    }

    public function update(User $user, Expense $expense): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        try {
            if (!$user->hasPermissionTo('Update:Expense')) {
                return false;
            }
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
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
        
        try {
            if (!$user->hasPermissionTo('Delete:Expense')) {
                return false;
            }
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            return false;
        }
        
        if ($user->hasRole('manager')) {
            // Manager chỉ xóa được trong ngày hiện tại
            return $expense->recorded_at->isToday() && !$expense->is_locked;
        }
        
        return false;
    }
}
