<?php

namespace App\Filament\Resources\Incomes\Schemas;

use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class IncomeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employee_id')
                    ->label('Nhân viên')
                    ->relationship('employee', 'name', fn (Builder $query) => $query->where('status', 'active'))
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('revenue')
                    ->label('Doanh thu (vé)')
                    ->default(0)
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
                                $set('revenue', $formatted);
                            }
                        }
                    })
                    ->rules(['regex:/^[\d.,]+$/']),
                TextInput::make('tip')
                    ->label('Tiền tip')
                    ->default(0)
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
                                $set('tip', $formatted);
                            }
                        }
                    })
                    ->rules(['regex:/^[\d.,]+$/']),
                TextInput::make('penalty')
                    ->label('Tiền phạt')
                    ->default(0)
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
                                $set('penalty', $formatted);
                            }
                        }
                    })
                    ->rules(['regex:/^[\d.,]+$/']),
                TextInput::make('facility')
                    ->label('Cơ sở vật chất')
                    ->default(0)
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
                                $set('facility', $formatted);
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
