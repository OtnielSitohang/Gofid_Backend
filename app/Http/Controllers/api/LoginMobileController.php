<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\member;
use App\Models\instruktur;
use App\Models\pegawai;
use Illuminate\Support\Facades\Validator;


class LoginMobileController extends Controller
{
    public function login(Request $request){
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

        $user = User::select('user.ID_USER', 'instruktur.ID_INSTRUKTUR', 'member.ID_MEMBER', 'pegawai.ID_PEGAWAI', 
        'pegawai.JABATAN_PEGAWAI', 'user.NAMA_USER', 'user.EMAIL_USER', 'user.PASSWORD_USER', 'user.FOTO_USER', 'user.TANGGAL_LAHIR_USER')
        ->join('pegawai' , 'user.ID_USER' , '=' , 'pegawai.ID_USER' , 'left outer')
        ->join ('member' , 'user.ID_USER', '=' , 'member.ID_USER' , 'left outer')
        ->join('instruktur', 'user.ID_USER', '=', 'instruktur.ID_USER' , 'left outer')
        ->where('EMAIL_USER', $EMAIL_USER)
        ->where('PASSWORD_USER',$PASSWORD_USER)
        ->first();


        if($user!=null){
            $response['message'] = "Login Berhasil";
            $response['data'] = $user;
            $response['status'] = true;
            return response($response);
        }
        else{
            $response['message'] = "Login Gagal";
            $response['data'] = null;
            $response['status'] = false;
            return response($response);
        }
    }
}
