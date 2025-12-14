<?php

namespace App\Filament\Infolists;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class ExpenseInfolist
{
    public static function configure(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Thông tin Chi')
                    ->schema([
                        TextEntry::make('amount')
                            ->label('Số tiền chi')
                            ->money('VND'),
                        TextEntry::make('note')
                            ->label('Ghi chú')
                            ->columnSpanFull(),
                        TextEntry::make('recorded_at')
                            ->label('Ngày ghi nhận')
                            ->date('d/m/Y'),
                    ])
                    ->columns(2),
                Section::make('Thông tin người tạo/cập nhật')
                    ->schema([
                        TextEntry::make('creator.name')
                            ->label('Người tạo'),
                        TextEntry::make('created_at')
                            ->label('Ngày tạo')
                            ->dateTime('d/m/Y H:i:s'),
                        TextEntry::make('updater.name')
                            ->label('Người cập nhật'),
                        TextEntry::make('updated_at')
                            ->label('Ngày cập nhật')
                            ->dateTime('d/m/Y H:i:s'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}

