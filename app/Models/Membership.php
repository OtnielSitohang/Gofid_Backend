<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;
    protected $table = 'membership';
    protected $primaryKey = 'ID_MEMBERSHIP';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'ID_MEMBERSHIP',
        'NO_STRUK_MEMBERSHIP',
        'MEM_ID_USER',
        'ID_MEMBER',
        'PEG_ID_USER',
        'ID_PEGAWAI',
        'TANGGAL_AKTIVASI_MEMBERSHIP',
        'TANGGAL_KADALUARSA_MEMBERSHIP',
    ];
}
