<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\user;
use App\Models\member;
use App\Models\pegawai;
use App\Models\booking_gym;
use App\Models\instruktur;
use App\Models\jadwal;
use App\Models\kelas;
use Hidehalo\Nanoid\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;


class BookingGymController extends Controller
{
    public function index(){

        $currentDate = Carbon::today();

        $BookingGym = booking_gym::with(['User', 'MEMBER'])
        // ->where('TANGGAL_GYM', $currentDate)
        ->get();


        return response([
            'message'=>'Success Tampil Data',
            'data' => $BookingGym
        ],200); 
    }

    public function PresensiGym($ID_BOOKING_PRESENSI_GYM){

        $idAwalStruck = 1;
        $CekIDTerakhirStruck = DB::select("SELECT NO_STRUK_PRESENSI_MEMBER_GYM FROM booking_gym ORDER BY NO_STRUK_PRESENSI_MEMBER_GYM desc limit 1;");
        if ($CekIDTerakhirStruck != null) {
            $idTerakhir = $CekIDTerakhirStruck[0]->NO_STRUK_PRESENSI_MEMBER_GYM;
            $idAwalStruck = intval(substr($idTerakhir, 6)) + 1;
        }
        $date = Carbon::now()->format('y.m');
        $formatNoStruckPresensi = $date . '.' . $idAwalStruck;

        $currentDate = Carbon::today();
        $newPresensiGym = booking_gym::find($ID_BOOKING_PRESENSI_GYM);


        $newPresensiGym->STATUS_PRESENSI = 1;
        $newPresensiGym->NO_STRUK_PRESENSI_MEMBER_GYM =$formatNoStruckPresensi;
        
        $newPresensiGym->update();
        return response()->json($newPresensiGym);
    }

    public function cekNotKadeluarsa($id){
        $membership = membership::find($id);
        if($membership->TANGGAL_KADALUARSA_MEMBERSHIP == null || $membership->TANGGAL_KADALUARSA_MEMBERSHIP < Carbon::now() ){
            return false;
        }
        return true;
    }

    public function cekAlreadyBookingToday($TANGGAL_BOOKING_GYM, $member){
        // dd($TANGGAL_BOOKING_GYM);
        $daftarBooking = booking_gym::where('TANGGAL_BOOKING_GYM', $TANGGAL_BOOKING_GYM )->where('ID_MEMBER',$member)->count();
        if($daftarBooking == 0){
            return false;
        }
        return true;
    }

    public function cekKuotaIsFull($tanggalSesi , $idSesi){
        $daftarBooking = booking_gym::where('SESI_BOOKING_GYM', $tanggalSesi )->where('id_sesi',$idSesi)->count();
        // $request->SESI_BOOKING_GYM
        if($daftarBooking < 10 ){
            return true;
        }
        return false;
    }

    public function cekBookingSame($tanggalSesi , $idSesi, $idMember){
        $daftarBooking = booking_gym::where('SESI_BOOKING_GYM', $tanggalSesi )->where('id_sesi',$idSesi)->where('ID_MEMBER',$idMember)->count();
        
        if($daftarBooking == 0 ){
            return false;
        }
        return true;
    }

    public function store(Request $request)
    {
        //* Cek Status Aktif Member
        if(!self::cekNotKadeluarsa($request->ID_MEMBER)){
            return Response(['message' => 'Akun Anda Sudah Kadeluarsa'],400);
        }
        //* Cek  Kuota
        if(!self::cekKuotaIsFull($request->SESI_BOOKING_GYM , $request->id_sesi)){
            return Response(['message' => 'Kuota Telah Penuh'],400);
        }
        if(self::cekAlreadyBookingToday(Carbon::today(),$request->ID_MEMBER)){
            return Response(['message' => 'Anda Telah Melakukan Booking Untuk Hari ini'],400);
        }
        //* Cek Apakah Booking Sama
        if(self::cekBookingSame($request->SESI_BOOKING_GYM,$request->id_sesi,$request->ID_MEMBER)){
            return Response(['message' => 'Anda Telah Melakuakn Booking pada sesi dan tanggal ini'],400);
        }

        $idAwalStruck = 1;
        $CekIDTerakhirStruck = DB::select("SELECT ID_BOOKING_PRESENSI_GYM  FROM booking_gym  ORDER BY ID_BOOKING_PRESENSI_GYM  desc limit 1 ");
        if ($CekIDTerakhirStruck != null) {
            $idTerakhir = $CekIDTerakhirStruck[0]->ID_BOOKING_PRESENSI_GYM;
            $idAwalStruck = intval(substr($idTerakhir, 6)) + 1;
        }
        $date = Carbon::now()->format('y.m');
        $formatIDStruck = $date . '.' . $idAwalStruck;

        try{
            $booking = booking_gym::create([
                'ID_BOOKING_PRESENSI_GYM ' => $formatIDStruck,
                'ID_USER' => $request->ID_USER,
                'ID_MEMBER' => $request->ID_MEMBER,
                'TANGGAL_BOOKING_GYM' => Carbon::now(),
                'SESI_BOOKING_GYM' => $request->SESI_BOOKING_GYM,
                // 'id_sesi' => $request->id_sesi,
            ]);
            
            return response([
                'message' => 'Berhasil Booking',
                'data' => $booking]);
        }catch(Exception $e){
            dd($e);
        }   
    }


    public function cancelBookingGym($ID_BOOKING_PRESENSI_GYM){
        $bookingGym = booking_gym::find($ID_BOOKING_PRESENSI_GYM);
        // $today = Carbon::today();
        $today = Carbon::now()->format('y.m.d');
        
        $batasCancel = Carbon::parse($bookingGym->TANGGAL_GYM)->subDay();
        if($batasCancel->greaterThanOrEqualTo($today)){
            $bookingGym->is_canceled =  1;
            $bookingGym->update();
            return response(
                [
                    'message' => 'Berhasil Membatalkan',
                    'data' => $bookingGym
                ]);
        }else{
            return response(['message' => 'Tidak bisa membatalkan, maksimal pembatalan H-1'],400);
        }
    }
}
