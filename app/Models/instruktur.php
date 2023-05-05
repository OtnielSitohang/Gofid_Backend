<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class instruktur extends Model
{
    use HasFactory;
    protected $table = 'instruktur';
    protected $primaryKey = 'ID_USER';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'ID_USER',
        'ID_INSTRUKTUR',
        'DESKRIPSI_INSTRUKTUR',
    ];

    // use SoftDeletes;
}
