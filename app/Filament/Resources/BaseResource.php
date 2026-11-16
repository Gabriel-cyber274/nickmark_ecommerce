<?php

namespace App\Filament\Resources;

use App\Models\Admin;
use Filament\Resources\Resource;

abstract class BaseResource extends Resource
{
    /**
     * Determine whether the resource should be displayed in the navigation.
     *
     * Super admins see everything. Product admins see only Products.
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->guard('admin')->user();

        return $user?->canAccessModel(static::$model) ?? false;
    }

    public static function canViewAny(): bool
    {
        $user = auth()->guard('admin')->user();

        return $user?->canAccessModel(static::$model) ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->guard('admin')->user();

        return $user?->canAccessModel(static::$model) ?? false;
    }

    public static function canView($record): bool
    {
        $user = auth()->guard('admin')->user();

        return $user?->canAccessModel(static::$model) ?? false;
    }

    public static function canEdit($record): bool
    {
        $user = auth()->guard('admin')->user();

        return $user?->canAccessModel(static::$model) ?? false;
    }

    public static function canDelete($record): bool
    {
        $user = auth()->guard('admin')->user();

        return $user?->canAccessModel(static::$model) ?? false;
    }

    public static function canDeleteAny(): bool
    {
        $user = auth()->guard('admin')->user();

        return $user?->canAccessModel(static::$model) ?? false;
    }

    /**
     * Get the currently authenticated admin user, if any.
     */
    protected static function actingAdmin(): ?Admin
    {
        return auth()->guard('admin')->user();
    }
}
