<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Tạo roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $employee = Role::firstOrCreate(['name' => 'employee']);

        // Tạo permissions cơ bản cho Shield
        $permissions = [
            // Resources
            'view_employee',
            'create_employee',
            'update_employee',
            'delete_employee',
            'view_income',
            'create_income',
            'update_income',
            'delete_income',
            'view_expense',
            'create_expense',
            'update_expense',
            'delete_expense',
            'view_user',
            'create_user',
            'update_user',
            'delete_user',
            'view_audit_log',
            
            // Pages
            'view_dashboard',
            'view_income_by_employee_stats',
            'view_expense_by_date_stats',
            
            // Widgets
            'view_stats_overview_widget',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Gán permissions cho roles
        $admin->syncPermissions(Permission::all());
        
        $manager->syncPermissions([
            'view_income',
            'create_income',
            'update_income',
            'view_expense',
            'create_expense',
            'update_expense',
            'view_dashboard',
            'view_income_by_employee_stats',
            'view_expense_by_date_stats',
            'view_stats_overview_widget',
        ]);

        $employee->syncPermissions([
            'view_income',
            'create_income',
            'view_dashboard',
            'view_income_by_employee_stats',
            'view_stats_overview_widget',
        ]);

        $this->command->info('Shield roles and permissions created successfully!');
    }
}

