<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ijininstruktur extends Model
{
    use HasFactory;
    protected $table = 'ijininstruktur';
    protected $primaryKey = 'ID_IJIN_INSTRUKTUR';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'ID_IJIN_INSTRUKTUR',
        'ID_INSTRUKTUR',
        'INS_ID_USER',
        'ID_INSTRUKTUR_PENGGANTI',
        'INS_PENGGANTI_ID_USER',
        'ID_JADWAL',
        'HARI_IZIN',
        'TANGGAL_IZIN',
        'TANGGAL_PENGAJUAN_IZIN',
        'SESI_IZIN',
        'KETERANGAN_IZIN',
        'STATUS_IZIN',
    ];

    public function Instruktur()
    {
        return $this->belongsTo(instruktur::class, 'ID_INSTRUKTUR', 'ID_INSTRUKTUR');

    }
    public function InstrukturPengganti()
    {
        return $this->belongsTo(instruktur::class, 'ID_INSTRUKTUR_PENGGANTI', 'ID_INSTRUKTUR');

    }
    public function InstrukturUserName()
    {
        return $this->belongsTo(User::class, 'INS_ID_USER', 'ID_USER');

    }
    public function InstrukturPenggantiUserName()
    {
        return $this->belongsTo(User::class, 'INS_PENGGANTI_ID_USER', 'ID_USER');

    }
}
