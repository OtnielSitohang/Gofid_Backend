<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class presensi_instruktur extends Model
{
    use HasFactory;
    protected $table = 'presensi_instruktur';
    protected $primaryKey = 'ID_PRESENSI_INSTRUKTUR';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'ID_PRESENSI_INSTRUKTUR',
        'PEG_ID_USER',
        'ID_PEGAWAI',
        'INS_ID_USER',
        'ID_INSTRUKTUR',
        'ID_JADWAL',
        'STATUS_PRESENSI_INSTRUKTUR',
        'TANGGAL_PRESENSI_INSTRUKTUR',
        'KETERANGAN_PRESENSI_INSTRUKTUR',
        'JAM_SELESAI_PRESENSI_INSTRUKTU',
    ];
}
