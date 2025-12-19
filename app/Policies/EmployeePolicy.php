<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        try {
            return $user->hasPermissionTo('ViewAny:Employee');
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            return false;
        }
    }

    public function view(User $user, Employee $employee): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        try {
            return $user->hasPermissionTo('View:Employee');
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
            return $user->hasPermissionTo('Create:Employee');
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            return false;
        }
    }

    public function update(User $user, Employee $employee): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        try {
            return $user->hasPermissionTo('Update:Employee');
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            return false;
        }
    }

    public function delete(User $user, Employee $employee): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        try {
            return $user->hasPermissionTo('Delete:Employee');
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            return false;
        }
    }
}
