<?php

namespace App\Filament\Resources\Expenses;

use App\Filament\Resources\Expenses\Pages\CreateExpense;
use App\Filament\Resources\Expenses\Pages\EditExpense;
use App\Filament\Resources\Expenses\Pages\ListExpenses;
use App\Filament\Resources\Expenses\Schemas\ExpenseForm;
use App\Filament\Resources\Expenses\Tables\ExpensesTable;
use App\Models\Expense;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationLabel = 'Chi';

    protected static ?string $modelLabel = 'Chi';

    protected static ?string $pluralModelLabel = 'Chi';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-down';

    protected static string|UnitEnum|null $navigationGroup = 'Giao dịch';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ExpenseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExpensesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExpenses::route('/'),
            'create' => CreateExpense::route('/create'),
            'edit' => EditExpense::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        
        if (!$user instanceof User) {
            return false;
        }
        
        try {
            return $user->hasPermissionTo('ViewAny:Expense') || $user->hasRole('admin');
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            // Permission chưa được tạo, chỉ admin mới có quyền
            return $user->hasRole('admin');
        }
    }
}
