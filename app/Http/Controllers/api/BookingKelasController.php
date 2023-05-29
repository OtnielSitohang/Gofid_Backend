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

class BookingKelasController extends Controller
{
    public function index(){

        $bookingKelas = booking_kelas::join('member', 'member.ID_MEMBER', '=', 'booking_kelas.ID_MEMBER')
        ->join('jadwal' , 'jadwal.ID_JADWAL' , '=' , 'booking_kelas.ID_JADWAL')
        ->join('kelas' , 'kelas.ID_KELAS' , '=' , 'jadwal.ID_KELAS')
        ->join('user' , 'user.ID_USER', '=' , 'booking_kelas.ID_USER')
        ->join('instruktur' , 'instruktur.ID_INSTRUKTUR', '=' , 'jadwal.ID_INSTRUKTUR')
        // ->join('user as us' , 'us.ID_USER', '=' , 'instruktur.ID_USER')
        ->get();

        return response([
            'message'=>'Success Tampil Data',
            'data' => $bookingKelas
        ],200); 
    }

    public function PresensiKelas($ID_BOOKING_KELAS){

        $idAwalStruck = 1;
        $CekIDTerakhirStruck = DB::select("SELECT NO_STRUK_PRESENSI_KELAS FROM booking_kelas ORDER BY NO_STRUK_PRESENSI_KELAS desc limit 1;");
        if ($CekIDTerakhirStruck != null) {
            $idTerakhir = $CekIDTerakhirStruck[0]->NO_STRUK_PRESENSI_KELAS;
            $idAwalStruck = intval(substr($idTerakhir, 6)) + 1;
        }
        $date = Carbon::now()->format('y.m');
        $formatNoStruckPresensi = $date . '.' . $idAwalStruck;

        $currentDate = Carbon::today();
        $newPresensiKelas = booking_kelas::find($ID_BOOKING_KELAS);


        $newPresensiKelas->STATUS_PRESENSI = 1;
        $newPresensiKelas->NO_STRUK_PRESENSI_KELAS =$formatNoStruckPresensi;
        // dd($newPresensiKelas);
        
        $newPresensiKelas->update();
        return response()->json($newPresensiKelas);
    }



}
