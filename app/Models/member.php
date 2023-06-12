<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class member extends Model
{
    use HasFactory;
    protected $table = 'member';
    protected $primaryKey = 'ID_MEMBER';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'ID_USER',
        'ID_MEMBER',    
        'ALAMAT_MEMBER',
        'TELEPON_MEMBER',
        'SISA_DEPOSIT_MEMBER',
        'TANGGAL_KADALUARSA_MEMBERSHIP',
        'TOTAL_KELAS',
        'TEMP_UANG',
        'TEMP_PAKET',
        'ID_KELAS',
    ];
}
