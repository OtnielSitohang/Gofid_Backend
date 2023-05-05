<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::get();
        return response ()-> json(['data' => $user]);
    }

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

    public function updatePassword(Request $request,$ID_USER)
    {
        // $newPasswordDefault = User::find($ID_USER);
        // var_dump($newPasswordDefault);exit;
        // $newPasswordDefault->PASSWORD_USER = $TANGGAL_LAHIR_USER;



        $input = $request->all();
        $newPasswordDefault = User::find($ID_USER);
        $newPasswordDefault->PASSWORD_USER = $request->input('PASSWORD_USER');
        //$newInstruktur->USER_FOTO = $request->input('USER_FOTO');

        $newPasswordDefault->update();
        return response()->json($newPasswordDefault);

        // $EMAIL_USER = $request->EMAIL_USER;
        // $PASSWORD_USER = $request->PASSWORD_USER;
        // $NewPassord = $request->NewPassword;
        
        // $user = User::where('EMAIL_USER', $EMAIL_USER)->where('PASSWORD_USER', $PASSWORD_USER)->first();
        // if(!$user){
            //     return response()->json(['message' => 'Password Lama Salah'], 400);
            // }
            // if($PASSWORD_USER == $NewPassord){
                //     return response()->json(['message' => 'Password Baru Tidak Boleh Sama Dengan Password Lama'], 400);
                // }
                
        // $input = $request->all();

        // if($user!=null){
        //     $user->PASSWORD_USER = $PASSWORD_USER;
        //     User::where('EMAIL_USER', $EMAIL_USER)->update(['PASSWORD_USER' => $NewPassord]);
        //     $New = User::where('EMAIL_USER', $EMAIL_USER)->first();
        //     $response['message'] = "Update Password Berhasil";
        //     $response['data'] = $New;
        //     $response['status'] = true;
        //     return response($response);
        // }
        // else{
        //     $response['message'] = "Update Password Gagal";
        //     $response['data'] = null;
        //     $response['status'] = false;
        //     return response($response);
        // }  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
