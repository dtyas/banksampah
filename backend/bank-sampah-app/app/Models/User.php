<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'email',
        'role',
        'status',
        'menu_access',
        'operational_access',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'menu_access' => 'array',
            'operational_access' => 'array',
        ];
    }

    public function nasabah()
    {
        return $this->hasOne(Nasabah::class);
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }
}
