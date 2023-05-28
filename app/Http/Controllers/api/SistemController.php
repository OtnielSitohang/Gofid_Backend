<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\user;
use App\Models\member;
use App\Models\pegawai;
use App\Models\instruktur;
use App\Models\DepositeKelas;
use App\Models\Membership;
use App\Models\kelas;
use Hidehalo\Nanoid\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SistemController extends Controller
{


    public function MendeaktifasiMember($ID_USER){
        $member = Member::where('ID_USER', $ID_USER)->first();
        if ($member) {
            $member->SISA_DEPOSIT_MEMBER = 0;
            $member->IS_DELETED_MEMBER = 1;
            $member->save();
        }
        
        $membership = Membership::where('MEM_ID_USER', $ID_USER)->first();
        // dd($membership);
        if ($membership) {
            $membership->TANGGAL_KADALUARSA_MEMBERSHIP = null;
            $membership->IS_DELETED_MEMBERSHIP = 1;
            $membership->save();
        }
        
        return response()->json($member);
        return response()->json($membership);
    }

    public function indexMendeaktifasiMember(){
        $data = Membership::where('TANGGAL_KADALUARSA_MEMBERSHIP', '<', date('Y-m-d H:i:s'))
        ->get();

        if(count($data) > 0)
        {
            return response()->json([
                'status' => 'success',
                'message' => 'Data Member Berhasil Ditampilkan',
                'data' => $data
            ], 200);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'Data Member Kosong',
            'data' => null
        ], 404);
    }

    
    public function ResetDepositeKelas($MEM_ID_USER){

        $depositKelas = DepositeKelas::where('MEM_ID_USER', $MEM_ID_USER)->first();
        if ($depositKelas) {
            $depositKelas->TANGGAL_KADALUARSA_DEPOSIT_KEL = null;
            $depositKelas->JUMLAH_DEPOSIT_KELAS = null;
            $depositKelas->BONUS_DEPOSIT_KELAS = null;
            $depositKelas->save();
        }
        // $NewResetDepositeKelas = DB::table('deposit_kelas')->where('MEM_ID_USER', $MEM_ID_USER)->update(['TANGGAL_KADALUARSA_DEPOSIT_KEL' => NULL], ['JUMLAH_DEPOSIT_KELAS' => 0], ['BONUS_DEPOSIT_KELAS'=> 0]);
        // return response(200);
    }

    public function indexResetDepositeKelas(){

        $data = DepositeKelas::join('kelas' , 'kelas.ID_KELAS' , '=' , 'deposit_kelas.ID_KELAS')
        ->join('user' , 'user.ID_USER' , '=' , 'deposit_kelas.MEM_ID_USER')
        ->where('TANGGAL_KADALUARSA_DEPOSIT_KEL', '<', date('Y-m-d H:i:s'))
        ->get();

        if(count($data) > 0)
        {
            return response()->json([
                'status' => 'success',
                'message' => 'Data Kelas Berhasil Ditampilkan',
                'data' => $data
            ], 200);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'Data Kelas Kosong',
            'data' => null
        ], 404);

    }
}
