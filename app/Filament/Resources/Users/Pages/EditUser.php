<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Pre-populate role field with user's first role (excluding admin)
        $user = $this->record;
        $firstRole = $user->roles()->where('name', '!=', 'admin')->first();
        if ($firstRole) {
            $data['role'] = $firstRole->id;
        }

        return $data;
    }
}
