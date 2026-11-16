<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_PRODUCT_ADMIN = 'product_admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => 'string',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admins';

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isProductAdmin(): bool
    {
        return $this->role === self::ROLE_PRODUCT_ADMIN;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function canAccessModel(string $modelClass): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->isProductAdmin()) {
            return $modelClass === Product::class;
        }

        return false;
    }
}
