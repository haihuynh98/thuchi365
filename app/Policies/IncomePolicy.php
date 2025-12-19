<?php

namespace App\Policies;

use App\Models\Income;
use App\Models\User;
use Carbon\Carbon;

class IncomePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('ViewAny:Income') || $user->hasRole('admin');
    }

    public function view(User $user, Income $income): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if (!$user->hasPermissionTo('View:Income')) {
            return false;
        }
        
        if ($user->hasRole('employee')) {
            // Nhân viên chỉ xem được của mình (nếu có liên kết)
            return true; // Tạm thời cho phép, có thể cần thêm logic
        }
        
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Create:Income') || $user->hasRole('admin');
    }

    public function update(User $user, Income $income): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if (!$user->hasPermissionTo('Update:Income')) {
            return false;
        }
        
        if ($user->hasRole('manager')) {
            // Manager chỉ sửa được trong ngày hiện tại
            return $income->recorded_at->isToday() && !$income->is_locked;
        }
        
        return false;
    }

    public function delete(User $user, Income $income): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if (!$user->hasPermissionTo('Delete:Income')) {
            return false;
        }
        
        if ($user->hasRole('manager')) {
            // Manager chỉ xóa được trong ngày hiện tại
            return $income->recorded_at->isToday() && !$income->is_locked;
        }
        
        return false;
    }
}
