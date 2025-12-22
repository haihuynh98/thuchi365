<?php

namespace App\Filament\Infolists;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class IncomeInfolist
{
    public static function configure(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Thông tin Thu')
                    ->schema([
                        TextEntry::make('employee.name')
                            ->label('Nhân viên'),
                        TextEntry::make('revenue')
                            ->label('Doanh thu (vé)')
                            ->money('VND'),
                        TextEntry::make('tip')
                            ->label('Tiền Tip')
                            ->money('VND'),
                        TextEntry::make('penalty')
                            ->label('Tiền phạt')
                            ->money('VND'),
                        TextEntry::make('facility')
                            ->label('Cơ sở vật chất')
                            ->money('VND'),
                        TextEntry::make('total')
                            ->label('Tổng thu')
                            ->money('VND')
                            ->state(fn ($record) => $record->total),
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

