<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class promo extends Model
{
    protected $table = 'promo';
    protected $primaryKey = 'ID_PROMO';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;


    protected $fillable = [
        'ID_PROMO',
        'POSTER_PROMO',
        'NAMA_PROMO',
        'MINIMAL_DEPOSIT_PROMO',
        'JENIS_PROMO',
        'KETERANGAN_PROMO',
        'BONUS_PROMO',
        'TOTAL_DEPOSIT',
    ];
}
