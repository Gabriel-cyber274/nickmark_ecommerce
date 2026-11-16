<?php

namespace App\Filament\Pages;

use App\Models\Admin;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->guard('admin')->user();

        return (bool) $user;
    }
}
