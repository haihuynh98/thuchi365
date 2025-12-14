<?php

namespace App\Console\Commands;

use App\Models\DailySummary;
use App\Models\Expense;
use App\Models\Income;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CloseDayCommand extends Command
{
    protected $signature = 'thuchi365:close-day {date?}';

    protected $description = 'Chốt ngày và gửi báo cáo Telegram';

    public function handle(TelegramService $telegramService): int
    {
        $date = $this->argument('date') 
            ? Carbon::parse($this->argument('date'))
            : Carbon::today();

        $this->info("Đang chốt ngày: {$date->format('d/m/Y')}");

        // Lock incomes and expenses
        Income::whereDate('recorded_at', $date)->update(['is_locked' => true]);
        Expense::whereDate('recorded_at', $date)->update(['is_locked' => true]);

        // Calculate totals
        $incomes = Income::whereDate('recorded_at', $date)->get();
        $expenses = Expense::whereDate('recorded_at', $date)->get();

        $totalIncome = $incomes->sum(fn ($income) => $income->total);
        $totalExpense = $expenses->sum('amount');
        $totalRevenue = $totalIncome - $totalExpense;

        // Breakdown by employee
        $breakdownByEmployee = $incomes
            ->groupBy('employee_id')
            ->map(function ($group) {
                $employee = $group->first()->employee;
                $total = $group->sum(fn ($income) => $income->total);
                return [
                    'employee_id' => $employee->employee_id ?? null,
                    'name' => $employee->name ?? 'N/A',
                    'total' => $total,
                ];
            })
            ->values()
            ->toArray();

        // Create or update daily summary
        DailySummary::updateOrCreate(
            ['date' => $date->format('Y-m-d')],
            [
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'total_revenue' => $totalRevenue,
                'breakdown_by_employee' => $breakdownByEmployee,
                'is_closed' => true,
            ]
        );

        $this->info("Đã lưu daily summary");

        // Send Telegram report
        if ($telegramService->sendDailyReport($date)) {
            $this->info("Đã gửi báo cáo Telegram");
        } else {
            $this->warn("Không thể gửi báo cáo Telegram (kiểm tra cấu hình)");
        }

        $this->info("Hoàn tất chốt ngày!");

        return Command::SUCCESS;
    }
}
