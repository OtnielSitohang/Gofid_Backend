<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KELAS;
use App\Models\jadwal_harian;
use App\Models\ijininstruktur;


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
        'ID_USER',
        'ID_INSTRUKTUR',
        'SESI_JADWAL',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'ID_KELAS', 'ID_KELAS');
    }

    public function jadwal_harian()
    {
        return $this->belongsTo(jadwal_harian::class, 'ID_JADWAL', 'ID_JADWAL');
    }




}
