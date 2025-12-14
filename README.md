# ThuChi365

WebApp quản lý thu – chi hằng ngày theo nhân viên, hỗ trợ thống kê chuyên sâu trên web và tự động gửi báo cáo doanh thu trong ngày qua Telegram.

## Yêu cầu

- PHP 8.2+
- Composer
- MySQL/PostgreSQL/SQLite
- Node.js & NPM (cho assets)

## Cài đặt

1. Clone repository và cài đặt dependencies:
```bash
composer install
npm install
```

2. Copy file `.env.example` thành `.env`:
```bash
cp .env.example .env
```

3. Tạo application key:
```bash
php artisan key:generate
```

4. Cấu hình database trong `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=thuchi365
DB_USERNAME=root
DB_PASSWORD=
```

5. Cấu hình Telegram Bot (tùy chọn):
```env
TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_CHAT_ID=your_chat_id
```

6. Chạy migrations và seeders:
```bash
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
```

7. Tạo user admin đầu tiên:
```bash
php artisan tinker
```
```php
$user = \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
]);
$user->assignRole('admin');
```

8. Build assets:
```bash
npm run build
```

9. Chạy server:
```bash
php artisan serve
```

Truy cập: http://localhost:8000/admin

## Sử dụng

### Chốt ngày và gửi báo cáo Telegram

Chạy lệnh để chốt ngày hiện tại:
```bash
php artisan thuchi365:close-day
```

Chốt ngày cụ thể:
```bash
php artisan thuchi365:close-day 2024-12-13
```

### Cron Job (tự động chốt ngày lúc 23:00)

Thêm vào crontab:
```bash
0 23 * * * cd /path/to/project && php artisan thuchi365:close-day
```

## Phân quyền

- **Admin**: Toàn quyền
- **Manager**: Tạo/sửa thu-chi (chỉ trong ngày), xem thống kê
- **Employee**: Tạo thu, xem thống kê của mình

## Cấu trúc dự án

- `app/Models/`: Models (Employee, Income, Expense, DailySummary)
- `app/Filament/Resources/`: Filament Resources
- `app/Policies/`: Authorization Policies
- `app/Services/`: Services (TelegramService)
- `app/Console/Commands/`: Artisan Commands
- `database/migrations/`: Database Migrations

## License

MIT
