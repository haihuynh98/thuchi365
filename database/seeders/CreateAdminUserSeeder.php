<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kiểm tra xem đã có admin chưa
        $adminExists = User::where('email', 'admin@thuchi365.com')->exists();

        if ($adminExists) {
            $this->command->warn('Admin user đã tồn tại!');
            return;
        }

        // Tạo user admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@thuchi365.com',
            'password' => Hash::make('admin123'),
        ]);

        // Gán role admin
        $admin->assignRole('admin');

        $this->command->info('✅ Đã tạo user admin thành công!');
        $this->command->info('📧 Email: admin@thuchi365.com');
        $this->command->info('🔑 Password: admin123');
        $this->command->warn('⚠️  Vui lòng đổi mật khẩu sau khi đăng nhập!');
    }
}
