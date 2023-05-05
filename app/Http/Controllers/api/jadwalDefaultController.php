<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\jadwal_default;
use App\Models\jadwal;
use App\Models\instruktur;
use Illuminate\Support\Facades\DB;
use Hidehalo\Nanoid\Client;
use Illuminate\Support\Facades\Validator;


class jadwalDefaultController extends Controller
{
    public function index()
    {
        $jadwal_default = jadwal_default::join('jadwal', 'jadwal_default.ID_JADWAL', '=' , 'jadwal.ID_JADWAL')
        ->join('user' , 'user.ID_USER', '=' , 'jadwal.ID_USER')
        ->join('kelas' , 'kelas.ID_KELAS', '=' , 'jadwal.ID_KELAS')
        ->where('IS_DELETED_JADWAL', NULL)
        ->where ('IS_DELETED_JADWAL_DEFAULT', NULL) 
        ->get();
        
        if(count($jadwal_default) > 0)
        {
            return response()->json([
                'status' => 'success',
                'message' => 'Data Jadwal Berhasil Ditampilkan',
                'data' => $jadwal_default
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Data Jadwal Kosong',
            'data' => null
        ], 404);

    }

    public function destroy($ID_JADWAL)
    {
        $jadwal_default1 = DB::table('jadwal')->where('ID_JADWAL', $ID_JADWAL)->update(['IS_DELETED_JADWAL' => 1]);
        $jadwal_default2 = DB::table('jadwal_default')->where('ID_JADWAL', $ID_JADWAL)->update(['IS_DELETED_JADWAL_DEFAULT' => 1]);
        return response(200);
    }

    public function store(Request $request){

        // {

        //     $validator = Validator::make($request->all(), [
        //         'ID_KELAS' => 'required',
        //         'ID_USER' => 'required',
        //         'ID_INSTRUKTUR' => 'unique:jadwal__umums,id_instruktur,NULL,id_jadwal_umum,id_instruktur,' 
        //         . $request->id_instruktur. ',hari_jadwal_umum,' 
        //         . $request->hari_jadwal_umum . ',jam_jadwal_umum,' 
        //         .$request->jam_jadwal_umum,

        //         'SESI_JADWAL' => 'required'
        //         'HARI_JADWAL_DEFAULT' => 'required'
        //     ],
        //     [   'id_instruktur.required' => 'Tidak Boleh Kosong!',
        //         'id_instruktur.unique' => 'Jadwal Instruktur Bertabrakan!']);
        //     if ($validator->fails()) {
        //         return response()->json($validator->errors(), 422);
        //     }
        //      //Fungsi Simpan Data ke dalam Database
        //      $jadwal_umum = Jadwal_Umum::create([
        //         'hari_jadwal_umum' => $request->hari_jadwal_umum,
        //         'id_kelas' => $request->id_kelas,
        //         'id_instruktur' => $request->id_instruktur,
        //         'jam_jadwal_umum' => $request->jam_jadwal_umum
        //     ]);
    
        //     return new JadwalUmumResource(true, 'Data Jadwal Umum Berhasil Ditambahkan!', $jadwal_umum);
        // }
    
        $client = new Client();
        $data = $request->json()->all();
        $ID_JADWAL = $client->generateId($size = 21);
        $ID_JADWAL_DEFAULT = $client->generateId($size = 21);
        $postJadwal['ID_JADWAL'] = $ID_JADWAL;
        $postJadwal['ID_KELAS'] = $data['ID_KELAS'];
        $postJadwal['JAD_ID_JADWAL'] = NULL;
        $postJadwal['ID_USER'] = $data['ID_USER'];
        $postJadwal['ID_INSTRUKTUR'] = $data['ID_INSTRUKTUR'];
        $postJadwal['SESI_JADWAL'] = $data['SESI_JADWAL'];
        
        $postJadwalDefault['ID_JADWAL'] = $ID_JADWAL;
        $postJadwalDefault['ID_JADWAL_DEFAULT'] = $ID_JADWAL_DEFAULT;
        $postJadwalDefault['HARI_JADWAL_DEFAULT'] = $data['HARI_JADWAL_DEFAULT'];

        jadwal::create([
            'ID_JADWAL' => $postJadwal['ID_JADWAL'],
            'ID_KELAS' => $postJadwal['ID_KELAS'],
            'JAD_ID_JADWAL' => $postJadwal['JAD_ID_JADWAL'],
            'ID_USER' => $postJadwal['ID_USER'],
            'ID_INSTRUKTUR' => $postJadwal['ID_INSTRUKTUR'],
            'SESI_JADWAL'=> $postJadwal['SESI_JADWAL'],
        ]);

        jadwal_default::create([
            'ID_JADWAL' => $postJadwal['ID_JADWAL'],
            'ID_JADWAL_DEFAULT' => $postJadwalDefault['ID_JADWAL_DEFAULT'],
            'HARI_JADWAL_DEFAULT' => $postJadwalDefault['HARI_JADWAL_DEFAULT'],
        ]);

        $jadwalAlias = jadwal::join('jadwal_default', 'jadwal.ID_JADWAL', '=', 'jadwal_default.ID_JADWAL')
        ->where('jadwal_default.IS_DELETED_JADWAL_DEFAULT', NULL)
        ->where('jadwal.IS_DELETED_JADWAL', NULL) 
        ->where('jadwal.ID_JADWAL', $postJadwal["ID_JADWAL"])
        ->get()[0];
        return response()->json($jadwalAlias);
    }

    public function update(Request $request, $ID_JADWAL)
    {

        $input = $request->all();
        $newJadwal = jadwal::find($ID_JADWAL);
        // $newJadwalDefault = jadwal::find($ID_JADWAL);
        $newJadwalDefault = jadwal_default::find($ID_JADWAL);
        // var_dump($newJadwal);exit;
        $newJadwal->ID_KELAS = $request->input('ID_KELAS');
        $newJadwal->ID_INSTRUKTUR = $request->input('ID_INSTRUKTUR');
        $newJadwal->ID_USER = $request->input('ID_USER');
        $newJadwalDefault->HARI_JADWAL_DEFAULT = $request->input('HARI_JADWAL_DEFAULT');
        $newJadwal->SESI_JADWAL = $request->input('SESI_JADWAL');
        //$newJadwal->USER_FOTO = $request->input('USER_FOTO');

        $newJadwal->update();
        $newJadwalDefault->update();
        return response()->json($newJadwal);
    }

}
