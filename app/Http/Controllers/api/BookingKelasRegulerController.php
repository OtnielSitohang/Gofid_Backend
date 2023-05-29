<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\user;
use App\Models\member;
use App\Models\pegawai;
use App\Models\booking_kelas;
use App\Models\DepositeKelas;
use App\Models\instruktur;
use App\Models\jadwal;
use App\Models\kelas;
use Hidehalo\Nanoid\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingKelasRegulerController extends Controller
{
    public function indexDepositeKelas(){
        $DepositeKelas = DepositeKelas::with(['User', 'MEMBER' , 'JADWAL' , 'KELAS', 'Instruktur'])->get();

        return response([
            'message'=>'Success Tampil Data',
            'data' => $DepositeKelas
        ],200); 

    }

    public function PresensiKelas($NO_STRUK_DEPOSIT_KELAS){
        $newPresensiKelas = DepositeKelas::find($NO_STRUK_DEPOSIT_KELAS);

        $newPresensiKelas->STATUS_PRESENSI = 1;
        $newPresensiKelas->TOTAL_KELAS -= 1;
        $newPresensiKelas->update();
        return response()->json($newPresensiKelas);
    }
}
