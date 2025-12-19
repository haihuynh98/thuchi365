<?php

namespace App\Filament\Pages;

use App\Models\Expense;
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

class ExpenseByDateStats extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Thống kê chi theo ngày';

    protected static ?string $title = 'Thống kê chi theo ngày';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';

    protected static string|UnitEnum|null $navigationGroup = 'Thống kê';

    protected static ?int $navigationSort = 2;

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
            ]);
    }

    protected function getTableQuery(): Builder
    {
        $query = Expense::query()
            ->selectRaw('DATE(recorded_at) as date, MIN(recorded_at) as recorded_at, MIN(id) as id');

        // Filter by date range if provided
        if ($this->startDate && $this->endDate) {
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end = Carbon::parse($this->endDate)->endOfDay();
            $query->whereBetween('recorded_at', [$start, $end]);
        } elseif ($this->startDate) {
            $start = Carbon::parse($this->startDate)->startOfDay();
            $query->where('recorded_at', '>=', $start);
        } elseif ($this->endDate) {
            $end = Carbon::parse($this->endDate)->endOfDay();
            $query->where('recorded_at', '<=', $end);
        }

        return $query
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

    public static function canAccess(): bool
    {
        $user = Auth::user();
        
        if (!$user instanceof \App\Models\User) {
            return false;
        }
        
        try {
            return $user->hasPermissionTo('View:ExpenseByDateStats') || $user->hasRole('admin');
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
            return $user->hasPermissionTo('View:ExpenseByDateStats') || $user->hasRole('admin');
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            return $user->hasRole('admin');
        }
    }
}
