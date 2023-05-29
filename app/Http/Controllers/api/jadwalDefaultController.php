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
        ->orderBy('HARI_JADWAL_DEFAULT', 'asc')
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

    private function isJadwalConflicting($ID_INSTRUKTUR, $HARI_JADWAL_DEFAULT, $SESI_JADWAL){
        $JadwalConflict = DB::select('SELECT j.*, hari_jadwal_default FROM jadwal j join jadwal_default jd on j.id_jadwal = jd.id_jadwal WHERE ID_INSTRUKTUR = ? AND HARI_JADWAL_DEFAULT = ? AND SESI_JADWAL = ?', [$ID_INSTRUKTUR, $HARI_JADWAL_DEFAULT, $SESI_JADWAL]);
        return count($JadwalConflict)>0;
        // var_dump($isJadwalConflicting);exit;
    }
    
    public function store(Request $request){

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
        

        if ($this->isJadwalConflicting($postJadwal['ID_INSTRUKTUR'], $postJadwalDefault['HARI_JADWAL_DEFAULT'], $postJadwal['SESI_JADWAL'])) {
            return response('Jadwal Instruktur Bertabrakan', 400);
        }
        
        jadwal::create([
            'ID_JADWAL' => $postJadwal['ID_JADWAL'],
            'ID_KELAS' => $postJadwal['ID_KELAS'],
            'JAD_ID_JADWAL' => $postJadwal['JAD_ID_JADWAL'],
            'ID_USER' => $postJadwal['ID_USER'],
            'ID_INSTRUKTUR' => $postJadwal['ID_INSTRUKTUR'],
            'SESI_JADWAL' => $postJadwal['SESI_JADWAL'],
        ]);

        jadwal_default::create([
            'ID_JADWAL' => $postJadwal['ID_JADWAL'],
            'ID_JADWAL_DEFAULT' => $postJadwalDefault['ID_JADWAL_DEFAULT'],
            'HARI_JADWAL_DEFAULT' => $postJadwalDefault['HARI_JADWAL_DEFAULT'],
        ]);

        $jadwalAlias = jadwal::join('jadwal_default', 'jadwal.ID_JADWAL', '=', 'jadwal_default.ID_JADWAL')
            ->where('jadwal_default.IS_DELETED_JADWAL_DEFAULT', NULL)
            ->where('jadwal.IS_DELETED_JADWAL', NULL)
            ->where('jadwal.ID_JADWAL', $postJadwal['ID_JADWAL'])
            ->get()[0];

        return response()->json($jadwalAlias);
    }        

    public function update(Request $request, $ID_JADWAL)
    {
        $input = $request->all();
        $newJadwal = jadwal::find($ID_JADWAL);
        // var_dump($newJadwal);exit;
        $newJadwalDefault = jadwal_default::find($ID_JADWAL);

        if ($this->isJadwalConflicting($request['ID_INSTRUKTUR'], $request['HARI_JADWAL_DEFAULT'], $request['SESI_JADWAL'])) {
            return response('Jadwal Instruktur Bertabrakan', 400);
        }

        $newJadwal->ID_INSTRUKTUR = $request->input('ID_INSTRUKTUR');
        $newJadwal->ID_KELAS = $request->input('ID_KELAS');
        $newJadwal->ID_USER = $request->input('ID_USER');
        $newJadwal->SESI_JADWAL = $request->input('SESI_JADWAL');
        $newJadwalDefault->HARI_JADWAL_DEFAULT = $request->input('HARI_JADWAL_DEFAULT');

        $newJadwal->update();
        $newJadwalDefault->update();
        return response()->json($newJadwal);
    }


    public function getJadwalMobile(){
        $jadwal = jadwal_default::orderByRaw("FIELD(HARI_JADWAL_DEFAULT, '1', '2', '3', '4', '5', '6', '7')")
        ->with(['jadwal'])
        ->get();

        return response(['data'=>$jadwal]);

    }

}
