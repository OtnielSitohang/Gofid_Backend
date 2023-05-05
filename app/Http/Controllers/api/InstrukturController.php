<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\instruktur;
use App\Models\User;
use Hidehalo\Nanoid\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class InstrukturController extends Controller
{
    public function index()
    {
        $instrukturs = instruktur::join('user', 'instruktur.ID_USER', '=', 'user.ID_USER')
        ->where('IS_DELETED_INSTRUKTUR', NULL)
        ->where ('IS_DELETED_USER', NULL) 
        ->get();
        
        if(count($instrukturs) > 0)
        {
            return response()->json([
                'status' => 'success',
                'message' => 'Data Instruktur Berhasil Ditampilkan',
                'data' => $instrukturs
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Data Instruktur Kosong',
            'data' => null
        ], 404);

    }


    public function store(Request $request)
    {
        $client = new Client();
        $data = $request->json()->all();
        $instruktur_temp = DB::select("SELECT ID_INSTRUKTUR FROM instruktur  ORDER BY ID_INSTRUKTUR desc limit 1 ");
        $id_temp = Str::substr($instruktur_temp[0]->ID_INSTRUKTUR, 1);
        $id =  "I". $id_temp+1;
        $ID_USER = $client->generateId($size = 21);
        $postUser['ID_USER'] = $ID_USER;
        $postUser['TANGGAL_DIBUAT_USER'] = date("Y-m-d");
        $postUser['NAMA_USER'] = $data['NAMA_USER'];
        $postUser['FOTO_USER'] = $data['FOTO_USER'];
        $postUser['EMAIL_USER'] = $data['EMAIL_USER'];
        $postUser['TANGGAL_LAHIR_USER'] = $data['TANGGAL_LAHIR_USER'];
        
        $postinstruktur['ID_USER'] = $ID_USER;
        $postinstruktur['ID_INSTRUKTUR'] = $id;
        $postinstruktur['DESKRIPSI_INSTRUKTUR'] = $data['DESKRIPSI_INSTRUKTUR'];
        User::create([
            'ID_USER' => $postUser['ID_USER'],
            'TANGGAL_DIBUAT_USER' => $postUser['TANGGAL_DIBUAT_USER'],
            'NAMA_USER' => $postUser['NAMA_USER'],
            'FOTO_USER' => $postUser['FOTO_USER'],
            'EMAIL_USER' => $postUser['EMAIL_USER'],
            'TANGGAL_LAHIR_USER' => $postUser['TANGGAL_LAHIR_USER'],
            'PASSWORD_USER'=> $postUser['TANGGAL_LAHIR_USER'],
        ]);

        instruktur::create([
            'ID_USER' => $postUser['ID_USER'],
            'ID_INSTRUKTUR' => $postinstruktur['ID_INSTRUKTUR'],
            'DESKRIPSI_INSTRUKTUR' => $postinstruktur['DESKRIPSI_INSTRUKTUR'],
         ]);
         
        $ins = instruktur::join('user', 'instruktur.ID_USER', '=', 'user.ID_USER')
        ->where('IS_DELETED_INSTRUKTUR', NULL)
        ->where('IS_DELETED_USER', NULL) 
        ->where('ID_INSTRUKTUR', $postinstruktur["ID_INSTRUKTUR"])
        ->get()[0];
        return response()->json($ins);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $newInstruktur = instruktur::find($id);
        $newUser = user::find($id);
        $newInstruktur->DESKRIPSI_INSTRUKTUR = $request->input('DESKRIPSI_INSTRUKTUR');

        $newUser->NAMA_USER = $request->input('NAMA_USER');
        $newUser->EMAIL_USER = $request->input('EMAIL_USER');
        $newUser->TANGGAL_LAHIR_USER = $request->input('TANGGAL_LAHIR_USER');
        //$newInstruktur->USER_FOTO = $request->input('USER_FOTO');

        $newInstruktur->update();
        $newUser->update();
        return response()->json($newInstruktur);
    }


    public function destroy($id)
    {
        $user1 = DB::table('instruktur')->where('ID_USER', $id)->update(['IS_DELETED_INSTRUKTUR' => 1]);
        $user2 = DB::table('user')->where('ID_USER', $id)->update(['IS_DELETED_USER' => 1]);
        return response(200);
        
    }
}
