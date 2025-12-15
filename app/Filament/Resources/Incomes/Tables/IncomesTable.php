<?php

namespace App\Filament\Resources\Incomes\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IncomesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name')
                    ->label('Nhân viên')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('revenue')
                    ->label('Doanh thu (vé)')
                    ->sortable()
                    ->state(function ($record) {
                        $base = (float) ($record->revenue ?? 0);
                        $withBonus = $base * 1.1;

                        $baseFormatted = number_format($base, 0, ',', '.');
                        $withBonusFormatted = number_format($withBonus, 0, ',', '.');

                        return "{$withBonusFormatted} ({$baseFormatted})";
                    }),
                TextColumn::make('penalty')
                    ->label('Phạt')
                    ->money('VND')
                    ->sortable(),
                TextColumn::make('facility')
                    ->label('CSVC')
                    ->money('VND')
                    ->sortable(),
                TextColumn::make('total')
                    ->label('Tổng thu')
                    ->money('VND')
                    ->sortable()
                    ->state(fn ($record) => $record->total),
                TextColumn::make('note')
                    ->label('Ghi chú')
                    ->limit(100)
                    ->tooltip(fn ($record) => $record->note)
                    ->wrap(false)
                    ->searchable(),
                TextColumn::make('recorded_at')
                    ->label('Ngày ghi nhận')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label('Người tạo')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updater.name')
                    ->label('Người cập nhật')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('employee_id')
                    ->label('Nhân viên')
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('recorded_at', 'desc')
            ->recordActions([
                EditAction::make(),
                Action::make('view')
                    ->label('Xem chi tiết')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Chi tiết Thu')
                    ->modalContent(function ($record) {
                        $logs = $record->auditLogs()->with('user')->latest()->get();
                        return view('filament.resources.view-income-detail', [
                            'record' => $record,
                            'logs' => $logs,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Đóng')
                    ->modalWidth('4xl'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
