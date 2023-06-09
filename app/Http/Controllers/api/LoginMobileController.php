<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\member;
use App\Models\instruktur;
use App\Models\pegawai;
use App\Models\kelas;
use DB;
use Illuminate\Support\Facades\Validator;


class LoginMobileController extends Controller
{
    public function login2(Request $request){
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'EMAIL_USER' => 'required',
            'PASSWORD_USER' => 'required'
        ]);
        
        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $EMAIL_USER = $request->EMAIL_USER;
        $PASSWORD_USER = $request->PASSWORD_USER;

        $user = DB::table('user')
        ->leftJoin('pegawai', 'user.ID_USER', '=', 'pegawai.ID_USER')
        ->leftJoin('instruktur', 'user.ID_USER', '=', 'instruktur.ID_USER')
        ->leftJoin('member', 'user.ID_USER', '=', 'member.ID_USER')
        ->leftJoin('kelas', 'member.ID_KELAS', '=', 'kelas.ID_KELAS')
        ->where('user.EMAIL_USER', $EMAIL_USER)
        ->where('user.PASSWORD_USER', $PASSWORD_USER)
        ->select(
            'user.ID_USER as user_ID_USER',
            'pegawai.ID_USER as pegawai_ID_USER',
            'instruktur.ID_USER as instruktur_ID_USER',
            'member.ID_USER as member_ID_USER',
            'user.*',
            'pegawai.*',
            'instruktur.*',
            'member.*',
            'kelas.*'
        )
    ->first();

    $kelas = [
        'ID_KELAS' => $user->ID_KELAS,
        'NAMA_KELAS' => $user->NAMA_KELAS,
        'HARGA_KELAS' => $user->HARGA_KELAS,
        'KAPASITAS_KELAS' => $user->KAPASITAS_KELAS,
    ];
    
    if ($user) {
        $response['message'] = "Login Berhasil";
        $response['data'] = $user;
        if ($user->ID_MEMBER != NULL  || $user->ID_INSTRUKTUR != NULL) {
            $response['data']->kelas = $kelas;
        }
        $response['status'] = true;
    } else {
        $response['message'] = "Login Gagal";
        $response['data'] = null;
        $response['status'] = false;
    }
    
    return response()->json($response);
}

        
}

