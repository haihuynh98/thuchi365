<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\CheckboxList;
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
                            ->label('Email')
                            ->email()
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
                        CheckboxList::make('roles')
                            ->label('Vai trò')
                            ->relationship('roles', 'name', fn ($query) => $query->whereNotIn('name', ['employee']))
                            ->options(Role::whereNotIn('name', ['employee'])->pluck('name', 'id'))
                            ->descriptions(
                                Role::whereNotIn('name', ['employee'])->get()->mapWithKeys(fn ($role) => [
                                    $role->id => $role->name === 'admin' ? 'Toàn quyền' : 'Quản lý'
                                ])
                            )
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
