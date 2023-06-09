<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class booking_gym extends Model
{
    use HasFactory;
    protected $table = 'booking_gym';
    protected $primaryKey = 'ID_BOOKING_PRESENSI_GYM';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'ID_BOOKING_PRESENSI_GYM',
        'ID_USER',
        'ID_MEMBER',
        'NO_STRUK_PRESENSI_MEMBER_GYM',
        'TANGGAL_BOOKING_GYM',
        'TANGGAL_GYM',
        'STATUS_PRESENSI',
        'IS_CANCELED',
        'SESI_BOOKING_GYM',
        'WAKTU_PRESENSI',
    ];



    public function User()
    {
        return $this->belongsTo(user::class, 'ID_USER', 'ID_USER');

    }
    public function MEMBER()
    {
        return $this->belongsTo(member::class, 'ID_MEMBER', 'ID_MEMBER');

    }
}
