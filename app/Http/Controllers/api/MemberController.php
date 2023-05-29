<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\member;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Hidehalo\Nanoid\Client;
use Illuminate\Support\Str;


class MemberController extends Controller
{
    public function index()
    {
        $member = member::join('user', 'member.ID_USER', '=', 'user.ID_USER')
        ->where('IS_DELETED_USER', NULL)
        ->where ('IS_DELETED_USER', NULL) 
        ->get();
        
        if(count($member) > 0)
        {
            return response()->json([
                'status' => 'success',
                'message' => 'Data Member Berhasil Ditampilkan',
                'data' => $member
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Data Member Kosong',
            'data' => null
        ], 404);

    }

    public function store(Request $request)
    {
        $client = new Client();
        $data = $request->json()->all();
        $member_temp = DB::select("SELECT ID_MEMBER FROM member ORDER BY ID_MEMBER desc limit 1 ");
        $id_temp = Str::substr($member_temp[0]->ID_MEMBER, 1);
        $idBaru = (int)substr($id_temp, 6) + 1;
        $idBaru = date('y') . "." . date('m') . ".".str_pad((string) $idBaru, 3, '0', STR_PAD_LEFT);
        

        $ID_USER = $client->generateId($size = 21);
        $postUser['ID_USER'] = $ID_USER;
        $postUser['TANGGAL_DIBUAT_USER'] = date("Y-m-d");
        $postUser['NAMA_USER'] = $data['NAMA_USER'];
        $postUser['FOTO_USER'] = $data['FOTO_USER'];
        $postUser['EMAIL_USER'] = $data['EMAIL_USER'];
        $postUser['TANGGAL_LAHIR_USER'] = $data['TANGGAL_LAHIR_USER'];

        

        $postMember['ID_USER'] = $ID_USER;
        $postMember['ID_MEMBER'] = $idBaru;
        $postMember['ALAMAT_MEMBER'] = $data['ALAMAT_MEMBER'];
        $postMember['TELEPON_MEMBER'] = $data['TELEPON_MEMBER'];
        $postMember['SISA_DEPOSIT_MEMBER'] = $data['SISA_DEPOSIT_MEMBER'];

        // $jsonMemberCreate = json_encode($postMember);
        
        User::create([
            'ID_USER' => $postUser['ID_USER'],
            'TANGGAL_DIBUAT_USER' => $postUser['TANGGAL_DIBUAT_USER'],
            'NAMA_USER' => $postUser['NAMA_USER'],
            'FOTO_USER' => $postUser['FOTO_USER'],
            'EMAIL_USER' => $postUser['EMAIL_USER'],
            'TANGGAL_LAHIR_USER' => $postUser['TANGGAL_LAHIR_USER'],
            'PASSWORD_USER'=> $postUser['TANGGAL_LAHIR_USER'],
        ]);
        member::create([
            'ID_USER' => $postUser['ID_USER'],
            'ID_MEMBER' => $postMember['ID_MEMBER'],
            'ALAMAT_MEMBER' => $postMember['ALAMAT_MEMBER'],
            'TELEPON_MEMBER' => $postMember['TELEPON_MEMBER'],
            'SISA_DEPOSIT_MEMBER' => $postMember['SISA_DEPOSIT_MEMBER'],
         ]);

         $membs = member::join('user', 'member.ID_USER', '=', 'user.ID_USER')
         ->where('IS_DELETED_MEMBER', NULL)
         ->where('IS_DELETED_USER', NULL) 
         ->where('ID_MEMBER', $postMember["ID_MEMBER"])
        //  var_dump($membs);exit;
         ->get()[0];
        return response()->json($membs);
    }

    public function destroy($id)
    {
        $user1 = DB::table('member')->where('ID_USER', $id)->update(['IS_DELETED_MEMBER' => 1]);
        $user2 = DB::table('user')->where('ID_USER', $id)->update(['IS_DELETED_USER' => 1]);
        return response(200);
    }


    public function update(Request $request, $id)
    {
        $input = $request->all();
        $newMember = member::find($id);
        $newUser = user::find($id);

        
        $newMember->ALAMAT_MEMBER = $request->input('ALAMAT_MEMBER');
        $newMember->TELEPON_MEMBER = $request->input('TELEPON_MEMBER');
        
        $newUser->NAMA_USER = $request->input('NAMA_USER');
        $newUser->EMAIL_USER = $request->input('EMAIL_USER');
        $newUser->TANGGAL_LAHIR_USER = $request->input('TANGGAL_LAHIR_USER');

        $newMember->update();
        $newUser->update();
        return response()->json($newMember);
    }
}
