<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class booking_kelas extends Model
{
    use HasFactory;
    protected $table = 'booking_kelas';
    protected $primaryKey = 'ID_BOOKING_KELAS';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'ID_BOOKING_KELAS',
        'ID_JADWAL',
        'ID_USER',
        'ID_MEMBER',
        'NO_STRUK_PRESENSI_KELAS',
        'TANGGAL_KELAS',
        'STATUS_PRESENSI',
        'IS_CANCELED',
        'SESI_BOOKING_KELAS',
        'TANGGAL_BOOKING_KELAS',
    ];

    public function Instruktur()
    {
        return $this->belongsTo(user::class, 'ID_USER', 'ID_USER');

    }
    public function MEMBER()
    {
        return $this->belongsTo(member::class, 'ID_MEMBER', 'ID_MEMBER');

    }
    public function jadwal()
{
    return $this->belongsTo(Jadwal::class, 'ID_JADWAL', 'ID_JADWAL');
}

    // public function KELAS()
    // {
    //     return $this->belongsTo(Kelas::class, 'ID_KELAS', 'ID_KELAS');
    // }

}
