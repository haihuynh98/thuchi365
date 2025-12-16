<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class MakeUserAdminCommand extends Command
{
    protected $signature = 'user:make-admin {email}';

    protected $description = 'Gán role admin cho user';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Không tìm thấy user với email: {$email}");
            return Command::FAILURE;
        }

        // Tạo role admin nếu chưa có
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Gán role admin
        $user->assignRole('admin');

        // Sync roles để đảm bảo chỉ có admin role
        $user->syncRoles(['admin']);

        $this->info("Đã gán role 'admin' cho user: {$user->name} ({$user->email})");
        $this->info("Roles hiện tại: " . $user->roles->pluck('name')->join(', '));

        return Command::SUCCESS;
    }
}

