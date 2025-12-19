<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Ensure admin role cannot be assigned (extra safety check)
        if (isset($data['role'])) {
            $role = Role::find($data['role']);
            if ($role && $role->name === 'admin') {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], []),
                    ['role' => 'Không được phép gán quyền admin']
                );
            }
        }

        return $data;
    }
}
