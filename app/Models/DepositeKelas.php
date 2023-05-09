<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositeKelas extends Model
{
    protected $table = 'deposit_kelas';
    protected $primaryKey = 'NO_STRUK_DEPOSIT_KELAS';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;


    protected $fillable = [
        'NO_STRUK_DEPOSIT_KELAS',
        'MEM_ID_USER',
        'ID_MEMBER',
        'ID_KELAS',
        'ID_PROMO',
        'PEG_ID_USER',
        'ID_PEGAWAI',
        'TANGGAL_DEPOSIT_KELAS',
        'TANGGAL_KADALUARSA_DEPOSIT_KEL',
        'JUMLAH_DEPOSIT_KELAS',
        'BONUS_DEPOSIT_KELAS',
    ];
}
