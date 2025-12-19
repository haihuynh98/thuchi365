<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin đăng nhập')
                    ->schema([
                        TextInput::make('name')
                            ->label('Họ và tên')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Tên đăng nhập')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Mật khẩu')
                            ->password()
                            ->required(fn ($livewire) => $livewire instanceof \App\Filament\Resources\Users\Pages\CreateUser)
                            ->dehydrated(fn ($state) => filled($state))
                            ->minLength(8)
                            ->helperText('Để trống nếu không muốn thay đổi mật khẩu')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Phân quyền')
                    ->schema([
                        Select::make('role')
                            ->label('Vai trò')
                            ->relationship('roles', 'name', fn ($query) => $query->whereNotIn('name', ['admin', 'employee']))
                            ->options(fn () => Role::whereNotIn('name', ['admin', 'employee'])->get()->mapWithKeys(fn ($role) => [
                                $role->id => match ($role->name) {
                                    'manager' => 'Quản lý',
                                    default => $role->name,
                                }
                            ]))
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
