<?php

namespace App\Filament\Pages;

use App\Models\Employee;
use App\Models\Income;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use UnitEnum;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class IncomeByEmployeeStats extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Thống kê thu theo nhân viên';

    protected static ?string $title = 'Thống kê thu theo nhân viên';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|UnitEnum|null $navigationGroup = 'Thống kê';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.income-by-employee-stats';

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
                TextColumn::make('employee_id')
                    ->label('Nhân viên')
                    ->searchable(),
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
        return Employee::query()
            ->whereHas('incomes')
            ->with('incomes');
    }

    protected function getDailyTotal($employee): float
    {
        return Income::where('employee_id', $employee->id)
            ->whereDate('recorded_at', Carbon::today())
            ->get()
            ->sum(fn ($income) => $income->total);
    }

    protected function getWeeklyTotal($employee): float
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return Income::where('employee_id', $employee->id)
            ->whereBetween('recorded_at', [$startOfWeek, $endOfWeek])
            ->get()
            ->sum(fn ($income) => $income->total);
    }

    protected function getMonthlyTotal($employee): float
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return Income::where('employee_id', $employee->id)
            ->whereBetween('recorded_at', [$startOfMonth, $endOfMonth])
            ->get()
            ->sum(fn ($income) => $income->total);
    }

    protected function getSelectedPeriodTotal($employee): float|string
    {
        if (!$this->startDate || !$this->endDate) {
            return '-';
        }

        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);

        return Income::where('employee_id', $employee->id)
            ->whereBetween('recorded_at', [$start, $end])
            ->get()
            ->sum(fn ($income) => $income->total);
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        
        if (!$user instanceof \App\Models\User) {
            return false;
        }
        
        try {
            return $user->hasPermissionTo('View:IncomeByEmployeeStats') || $user->hasRole('admin');
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            return $user->hasRole('admin');
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        
        if (!$user instanceof \App\Models\User) {
            return false;
        }
        
        try {
            return $user->hasPermissionTo('View:IncomeByEmployeeStats') || $user->hasRole('admin');
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            return $user->hasRole('admin');
        }
    }
}
