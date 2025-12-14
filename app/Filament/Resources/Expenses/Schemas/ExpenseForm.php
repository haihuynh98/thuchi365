<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('amount')
                    ->label('Số tiền chi')
                    ->required()
                    ->prefix('₫')
                    ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : '')
                    ->dehydrateStateUsing(function ($state) {
                        if (empty($state)) return 0;
                        $cleaned = preg_replace('/[^0-9]/', '', (string) $state);
                        return $cleaned ? (float) $cleaned : 0;
                    })
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            $cleaned = preg_replace('/[^0-9]/', '', (string) $state);
                            if ($cleaned) {
                                $formatted = number_format((float) $cleaned, 0, ',', '.');
                                $set('amount', $formatted);
                            }
                        }
                    })
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
                    ->displayFormat('d/m/Y'),
            ]);
    }
}
