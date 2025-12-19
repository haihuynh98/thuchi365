<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\Expense;
use App\Models\Income;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    public static function canView(): bool
    {
        $user = Auth::user();
        
        if (!$user instanceof \App\Models\User) {
            return false;
        }
        
        return $user->hasPermissionTo('View:StatsOverviewWidget') || $user->hasRole('admin');
    }

    protected function getStats(): array
    {
        $today = Carbon::today();
        
        $totalIncome = Income::whereDate('recorded_at', $today)
            ->get()
            ->sum(fn ($income) => $income->total);
        
        $totalExpense = Expense::whereDate('recorded_at', $today)
            ->sum('amount');
        
        $totalRevenue = $totalIncome - $totalExpense;
        
        $activeEmployees = Employee::where('status', 'active')->count();

        return [
            Stat::make('Tổng thu hôm nay', number_format($totalIncome, 0, ',', '.') . ' ₫')
                ->description('Doanh thu + Tip + Phạt + CSVC')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Tổng chi hôm nay', number_format($totalExpense, 0, ',', '.') . ' ₫')
                ->description('Chi phí phát sinh')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
            Stat::make('Tổng doanh thu hôm nay', number_format($totalRevenue, 0, ',', '.') . ' ₫')
                ->description('Thu - Chi')
                ->descriptionIcon('heroicon-m-calculator')
                ->color($totalRevenue >= 0 ? 'success' : 'danger'),
            Stat::make('Số nhân viên hoạt động', $activeEmployees)
                ->description('Đang làm việc')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}
