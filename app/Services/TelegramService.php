<?php

namespace App\Services;

use App\Models\DailySummary;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $botToken;
    protected ?string $chatId;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token', '');
        $this->chatId = config('services.telegram.chat_id', '');
    }

    public function sendDailyReport(Carbon $date): bool
    {
        if (empty($this->botToken) || empty($this->chatId)) {
            Log::warning('Telegram bot token or chat ID not configured');
            return false;
        }

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
                    'employee_id' => $employee->employee_id ?? 'N/A',
                    'total' => $total,
                ];
            })
            ->sortByDesc('total');

        $message = $this->formatDailyReport($date, $totalIncome, $totalExpense, $totalRevenue, $breakdownByEmployee);

        return $this->sendMessage($message);
    }

    protected function formatDailyReport(Carbon $date, float $totalIncome, float $totalExpense, float $totalRevenue, $breakdownByEmployee): string
    {
        $dateFormatted = $date->format('d/m/Y');
        
        $message = "📊 BÁO CÁO TRONG NGÀY {$dateFormatted}\n\n";
        $message .= "💰 Tổng doanh thu: " . number_format($totalRevenue, 0, ',', '.') . " ₫\n\n";
        $message .= "Trong đó:\n";
        $message .= "- Tổng thu: " . number_format($totalIncome, 0, ',', '.') . " ₫\n";
        $message .= "- Tổng chi: " . number_format($totalExpense, 0, ',', '.') . " ₫\n\n";

        if ($breakdownByEmployee->isNotEmpty()) {
            $message .= "👥 Theo nhân viên:\n";
            foreach ($breakdownByEmployee as $item) {
                $message .= "- {$item['employee_id']}: " . number_format($item['total'], 0, ',', '.') . " ₫\n";
            }
        } else {
            $message .= "👥 Theo nhân viên: Không có dữ liệu\n";
        }

        return $message;
    }

    protected function sendMessage(string $message): bool
    {
        try {
            $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                Log::info('Telegram daily report sent successfully');
                return true;
            }

            Log::error('Failed to send Telegram message', [
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Exception while sending Telegram message', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}

