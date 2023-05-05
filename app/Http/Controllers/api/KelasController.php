<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\kelas;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    public function index()
    {
        // $user = User::get();
        $kelas = kelas::where ('IS_DELETED_KELAS', NULL) 
        ->get();
        
        if(count($kelas) > 0)
        {
            return response()->json([
                'status' => 'success',
                'message' => 'Data Kelas Berhasil Ditampilkan',
                'data' => $kelas
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Data Kelas Kosong',
            'data' => null
        ], 404);

    }
}
