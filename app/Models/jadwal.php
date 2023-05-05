<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal';
    protected $primaryKey = 'ID_JADWAL';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'ID_JADWAL',
        'ID_KELAS',
        'JAD_ID_JADWAL',
        'ID_USER',
        'ID_INSTRUKTUR',
        'SESI_JADWAL',
    ];
}
