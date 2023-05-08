<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositUang extends Model
{
    use HasFactory;
    protected $table = 'deposit_uang';
    protected $primaryKey = 'NO_STRUK_DEPOSIT_UANG';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'NO_STRUK_DEPOSIT_UANG',
        'MEM_ID_USER',
        'ID_MEMBER',
        'ID_PROMO',
        'PEG_ID_USER',
        'ID_PEGAWAI',
        'JUMLAH_DEPOSIT_UANG',
        'BONUS_DEPOSIT_UANG',
        'TANGGAL_DEPOSIT_UANG',
        'TOTAL_DEPOSIT',
    ];
}
