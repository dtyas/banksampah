<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['nasabah_id', 'saldo'];
    protected $casts = [
        'meta' => 'array',
    ];
    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }
}
