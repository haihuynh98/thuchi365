<?php

namespace App\Providers;

use App\Models\Employee;
use App\Models\Expense;
use App\Models\Income;
use App\Observers\ExpenseObserver;
use App\Observers\IncomeObserver;
use App\Policies\EmployeePolicy;
use App\Policies\ExpensePolicy;
use App\Policies\IncomePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(Income::class, IncomePolicy::class);
        Gate::policy(Expense::class, ExpensePolicy::class);

        // Bypass Shield authorization cho Dashboard - cho phép tất cả user đã đăng nhập
        Gate::before(function ($user, $ability) {
            // Nếu là view Dashboard, cho phép tất cả user đã đăng nhập
            if ($ability === 'view_dashboard' || $ability === 'view_App\\Filament\\Pages\\Dashboard') {
                return true;
            }
            return null;
        });

        // Register observers
        Income::observe(IncomeObserver::class);
        Expense::observe(ExpenseObserver::class);
    }
}
