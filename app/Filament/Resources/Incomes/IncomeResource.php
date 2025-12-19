<?php

namespace App\Filament\Resources\Incomes;

use App\Filament\Resources\Incomes\Pages\CreateIncome;
use App\Filament\Resources\Incomes\Pages\EditIncome;
use App\Filament\Resources\Incomes\Pages\ListIncomes;
use App\Filament\Resources\Incomes\Schemas\IncomeForm;
use App\Filament\Resources\Incomes\Tables\IncomesTable;
use App\Models\Income;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;

    protected static ?string $navigationLabel = 'Thu';

    protected static ?string $modelLabel = 'Thu';

    protected static ?string $pluralModelLabel = 'Thu';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static string|UnitEnum|null $navigationGroup = 'Giao dịch';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return IncomeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IncomesTable::configure($table);
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
            'index' => ListIncomes::route('/'),
            'create' => CreateIncome::route('/create'),
            'edit' => EditIncome::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        
        if (!$user instanceof User) {
            return false;
        }
        
        try {
            return $user->hasPermissionTo('ViewAny:Income') || $user->hasRole('admin');
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            // Permission chưa được tạo, chỉ admin mới có quyền
            return $user->hasRole('admin');
        }
    }
}
