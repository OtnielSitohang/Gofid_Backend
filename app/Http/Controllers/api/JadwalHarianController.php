<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\jadwal_default;
use App\Models\jadwal;
use App\Models\jadwal_harian;
use App\Models\instruktur;
use Illuminate\Support\Facades\DB;
use Hidehalo\Nanoid\Client;
use Illuminate\Support\Facades\Validator;


class JadwalHarianController extends Controller
{
    public function index()
    {
        $jadwal_harian = jadwal_harian::join('jadwal', 'jadwal_harian.ID_JADWAL', '=' , 'jadwal.ID_JADWAL')
        ->join('user' , 'user.ID_USER', '=' , 'jadwal.ID_USER')
        ->join('kelas' , 'kelas.ID_KELAS', '=' , 'jadwal.ID_KELAS')
        ->where('IS_DELETED_JADWAL', NULL)
        ->where ('IS_DELETED_JADWAL_HARIAN', NULL) 
        ->get();
        
        if(count($jadwal_harian) > 0)
        {
            return response()->json([
                'status' => 'success',
                'message' => 'Data Jadwal Berhasil Ditampilkan',
                'data' => $jadwal_harian
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Data Jadwal Kosong',
            'data' => null
        ], 404);

    }

}
