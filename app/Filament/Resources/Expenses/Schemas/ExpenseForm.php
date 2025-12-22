<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('amount')
                    ->label('Số tiền chi')
                    ->default(0)
                    ->required()
                    ->prefix('₫')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters('.,')
                    ->formatStateUsing(function ($state) {
                        if ($state === null || $state === '') {
                            return null;
                        }

                        // Convert "100000.00" -> "100000" (không hiển thị số lẻ)
                        $value = (float) $state;

                        return (string) (int) round($value);
                    })
                    ->dehydrateStateUsing(function ($state) {
                        if (empty($state)) return 0;
                        $cleaned = preg_replace('/[^0-9]/', '', (string) $state);
                        return $cleaned ? (float) $cleaned : 0;
                    })
                    ->live(onBlur: true)
                    ->rules(['regex:/^[\d.,]+$/']),
                Textarea::make('note')
                    ->label('Ghi chú')
                    ->rows(3)
                    ->columnSpanFull(),
                DatePicker::make('recorded_at')
                    ->label('Ngày ghi nhận')
                    ->default(now())
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->hiddenOn(['create']),
            ]);
    }
}
