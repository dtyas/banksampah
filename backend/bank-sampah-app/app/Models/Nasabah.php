<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nasabah extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'nasabah';

    protected $fillable = [
        'user_id',
        'nama',
        'alamat',
        'no_hp',
        'payout_channel',
        'account_number',
        'account_holder_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}
