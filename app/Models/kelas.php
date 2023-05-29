<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Jadwal;


class kelas extends Model
{
    use HasFactory;
    protected $table = 'kelas';
    protected $primaryKey = 'ID_KELAS';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'ID_KELAS',
        'NAMA_KELAS',
        'HARGA_KELAS',
        'KAPASITAS_KELAS',
    ];

    public function jadwal()
{
    return $this->hasMany(Jadwal::class, 'ID_KELAS', 'ID_KELAS');
}



}
