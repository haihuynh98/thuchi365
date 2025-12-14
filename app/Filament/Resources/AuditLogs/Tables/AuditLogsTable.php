<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use App\Models\Expense;
use App\Models\Income;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('auditable_type')
                    ->label('Loại')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Income::class => 'Thu',
                        Expense::class => 'Chi',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Income::class => 'success',
                        Expense::class => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('auditable_id')
                    ->label('ID Record')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('action')
                    ->label('Hành động')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'Tạo mới',
                        'updated' => 'Cập nhật',
                        'deleted' => 'Xóa',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('user.name')
                    ->label('Người thực hiện')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('old_values')
                    ->label('Giá trị cũ')
                    ->formatStateUsing(fn (?array $state): string => $state ? json_encode($state, JSON_UNESCAPED_UNICODE) : '-')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->old_values ? json_encode($record->old_values, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : null)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('new_values')
                    ->label('Giá trị mới')
                    ->formatStateUsing(fn (?array $state): string => $state ? json_encode($state, JSON_UNESCAPED_UNICODE) : '-')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->new_values ? json_encode($record->new_values, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : null)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Thời gian')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Hành động')
                    ->options([
                        'created' => 'Tạo mới',
                        'updated' => 'Cập nhật',
                        'deleted' => 'Xóa',
                    ]),
                SelectFilter::make('auditable_type')
                    ->label('Loại')
                    ->options([
                        Income::class => 'Thu',
                        Expense::class => 'Chi',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([])
            ->toolbarActions([]);
    }
}
