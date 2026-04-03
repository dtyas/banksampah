<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sampah extends Model
{
    use HasFactory;

    protected $table = 'sampah';

    protected $fillable = [
        'kategori_sampah_id',
        'nama_sampah',
        'harga_per_kg',
    ];

    public function kategoriSampah()
    {
        return $this->belongsTo(KategoriSampah::class);
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}
