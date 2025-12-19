<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('ViewAny:Employee') || $user->hasRole('admin');
    }

    public function view(User $user, Employee $employee): bool
    {
        return $user->hasPermissionTo('View:Employee') || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Create:Employee') || $user->hasRole('admin');
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->hasPermissionTo('Update:Employee') || $user->hasRole('admin');
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $user->hasPermissionTo('Delete:Employee') || $user->hasRole('admin');
    }
}
