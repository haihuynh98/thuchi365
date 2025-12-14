<?php

namespace App\Filament\Pages;

use App\Models\Expense;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ExpenseByDateStats extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Thống kê chi theo ngày';

    protected static ?string $title = 'Thống kê chi theo ngày';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?int $navigationSort = 11;

    protected string $view = 'filament.pages.expense-by-date-stats';

    public ?string $startDate = null;
    public ?string $endDate = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getForms(): array
    {
        return [
            'form',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('startDate')
                ->label('Từ ngày')
                ->displayFormat('d/m/Y')
                ->native(false)
                ->live()
                ->afterStateUpdated(function () {
                    $this->resetTable();
                }),
            DatePicker::make('endDate')
                ->label('Đến ngày')
                ->displayFormat('d/m/Y')
                ->native(false)
                ->live()
                ->afterStateUpdated(function () {
                    $this->resetTable();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('date')
                    ->label('Ngày')
                    ->date('d/m/Y')
                    ->sortable(false)
                    ->state(function ($record) {
                        return $record->date ?? $record->recorded_at;
                    }),
                TextColumn::make('daily_total')
                    ->label('Ngày')
                    ->money('VND')
                    ->state(function ($record) {
                        return $this->getDailyTotal($record);
                    }),
                TextColumn::make('weekly_total')
                    ->label('Tuần')
                    ->money('VND')
                    ->state(function ($record) {
                        return $this->getWeeklyTotal($record);
                    }),
                TextColumn::make('monthly_total')
                    ->label('Tháng')
                    ->money('VND')
                    ->state(function ($record) {
                        return $this->getMonthlyTotal($record);
                    }),
                TextColumn::make('selected_period_total')
                    ->label('Ngày chọn')
                    ->formatStateUsing(function ($state) {
                        return $state === '-' ? '-' : number_format($state, 0, ',', '.') . ' ₫';
                    })
                    ->state(function ($record) {
                        return $this->getSelectedPeriodTotal($record);
                    }),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        return Expense::query()
            ->selectRaw('DATE(recorded_at) as date, MIN(recorded_at) as recorded_at, MIN(id) as id')
            ->groupByRaw('DATE(recorded_at)')
            ->orderByRaw('DATE(recorded_at) DESC');
    }

    protected function getDailyTotal($record): float
    {
        $date = $record->date ?? $record->recorded_at;

        return Expense::whereDate('recorded_at', $date)
            ->sum('amount');
    }

    protected function getWeeklyTotal($record): float
    {
        $date = Carbon::parse($record->date ?? $record->recorded_at);
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        return Expense::whereBetween('recorded_at', [$startOfWeek, $endOfWeek])
            ->sum('amount');
    }

    protected function getMonthlyTotal($record): float
    {
        $date = Carbon::parse($record->date ?? $record->recorded_at);
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        return Expense::whereBetween('recorded_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');
    }

    protected function getSelectedPeriodTotal($record): float|string
    {
        if (!$this->startDate || !$this->endDate) {
            return '-';
        }

        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();
        $date = Carbon::parse($record->date ?? $record->recorded_at);

        if ($date->between($start, $end)) {
            return Expense::whereDate('recorded_at', $date->format('Y-m-d'))
                ->sum('amount');
        }

        return 0;
    }
}
