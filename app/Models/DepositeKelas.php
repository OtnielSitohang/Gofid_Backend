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
        'TOTAL_KELAS',
        'STATUS_PRESENSI',
        'BONUS_DEPOSIT_KELAS',
        'TOTAL_HARGA_DEPOSITE_KELAS',
    ];

    

    public function User()
    {
        return $this->hasOne(user::class, 'ID_USER', 'MEM_ID_USER');

    }
    public function MEMBER()
    {
        return $this->hasOne(member::class, 'ID_MEMBER', 'ID_MEMBER');
    }
    public function JADWAL()
    {
        return $this->hasOne(jadwal::class, 'ID_JADWAL', 'ID_JADWAL');
    }
    public function KELAS()
    {
        return $this->hasOne(kelas::class, 'ID_KELAS', 'ID_KELAS');
    }
    public function Instruktur()
    {
        return $this->hasOne(user::class, 'ID_USER', 'PEG_ID_USER');
    }

    // public function InstrukturName()
    // {
    //     return $this->hasOne(user::class, 'ID_USER', 'PEG_ID_USER');
    // }
}
