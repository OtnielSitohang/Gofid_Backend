<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jadwal_harian extends Model
{
    use HasFactory;
    protected $table = 'jadwal_harian';
    protected $primaryKey = 'ID_JADWAL';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'ID_JADWAL',
        'ID_JADWAL_HARIAN',
        'TANGGAL_JADWAL_HARIAN',
        'STATUS'
    ];
}
