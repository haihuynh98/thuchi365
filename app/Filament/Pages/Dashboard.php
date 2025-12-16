<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Auth\Authenticatable;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
        ];
    }

    public static function canAccess(): bool
    {
        // Cho phép tất cả user đã đăng nhập truy cập dashboard
        // Filament sẽ tự kiểm tra authentication qua middleware
        return true;
    }
}

