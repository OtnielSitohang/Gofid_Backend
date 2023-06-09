<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ijininstruktur;
use App\Models\user;
use App\Models\member;
use App\Models\pegawai;
use App\Models\instruktur;
use App\Models\jadwal;
use App\Models\kelas;
use App\Models\jadwal_harian;
use Hidehalo\Nanoid\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;


class IjinInstrukturController extends Controller
{
    public function index(){

        $ijininstruktur = IjinInstruktur::with(['instruktur', 'InstrukturUserName' , 'InstrukturPengganti' , 'InstrukturPenggantiUserName'])->get();

        return response([
            'message'=>'Success Tampil Data',
            'data' => $ijininstruktur
        ],200); 
    }
    public function update(Request $request, $ID_IJIN_INSTRUKTUR){

        $input = $request->all();
        $newIzinInstruktur = ijininstruktur::find($ID_IJIN_INSTRUKTUR);
        $newIzinInstruktur->STATUS_IZIN = $request->input('STATUS_IZIN');
        // dd($input);
        // dd($newIzinInstruktur);
        
        $newIzinInstruktur->update();
        return response()->json($newIzinInstruktur);

    }

    public function store(Request $request){
        $client = new Client();
        $data = $request->json()->all();

        $cekIdTerakhir = DB::select("SELECT ID_IJIN_INSTRUKTUR FROM ijininstruktur ORDER BY ID_IJIN_INSTRUKTUR DESC LIMIT 1");
        $lastId = $cekIdTerakhir[0]->ID_IJIN_INSTRUKTUR;
        $newId = $lastId + 1;

        $tanggalNow = Carbon::now();
        // $format = $idAwal;

        ijininstruktur::create([
            'ID_IJIN_INSTRUKTUR' => $newId,
            // 'ID_IJIN_INSTRUKTUR' => $format,
            'ID_INSTRUKTUR' => $request['ID_INSTRUKTUR'],
            'INS_ID_USER' =>  $request['INS_ID_USER'],
            'ID_INSTRUKTUR_PENGGANTI' =>  $request['ID_INSTRUKTUR_PENGGANTI'],
            'INS_PENGGANTI_ID_USER' =>  $request['INS_PENGGANTI_ID_USER'],
            'ID_JADWAL' =>  $request['ID_JADWAL'],
            'TANGGAL_IZIN' =>  $request['TANGGAL_IZIN'],
            'TANGGAL_PENGAJUAN_IZIN' =>  $tanggalNow,
            'SESI_IZIN' =>  $request['SESI_IZIN'],
            'KETERANGAN_IZIN' =>  $request['KETERANGAN_IZIN'],
            'STATUS_IZIN' => 0,
        ]);

        $updateIzin = jadwal_harian::find($data['ID_JADWAL']);
        // dd($updateIzin);
        $updateIzin->ID_INSTRUKTUR_PENGGANTI = $request['ID_INSTRUKTUR_PENGGANTI'];
        $updateIzin->ID_USER_PENGGANTI  = $request['INS_PENGGANTI_ID_USER'];
        $updateIzin->ID_IZIN  = $newId;
        $updateIzin->STATUS_IZIN  = 0;
        $updateIzin->update();
    }


    // public function showIzinByID($ID_INSTRUKTUR)
    // {
    //     $ijininstruktur = IjinInstruktur::with(['instruktur', 'InstrukturUserName', 'InstrukturPengganti', 'InstrukturPenggantiUserName'])
    //     ->join('jadwal', 'jadwal.ID_JADWAL' , '=' , 'IjinInstruktur.ID_JADWAL')
    //     ->join('kelas' , 'kelas.ID_KELAS' , 'jadwal.ID_KELAS')
    //         ->where('ID_INSTRUKTUR', $ID_INSTRUKTUR)
    //         ->get();

    //     $jumlahData = $ijininstruktur->count();

    //     return [
    //         'data' => $ijininstruktur,
    //         'jumlahData' => $jumlahData
    //     ];
    // }


    public function showIzinByID($ID_INSTRUKTUR)
    {
        $ijininstruktur = IjinInstruktur::with(['instruktur', 'InstrukturUserName', 'InstrukturPengganti', 'InstrukturPenggantiUserName'])
            ->join('jadwal', 'jadwal.ID_JADWAL', '=', 'IjinInstruktur.ID_JADWAL')
            ->join('kelas', 'kelas.ID_KELAS', 'jadwal.ID_KELAS')
            ->where('IjinInstruktur.ID_INSTRUKTUR', $ID_INSTRUKTUR)
            ->get();

        $jumlahData = $ijininstruktur->count();

        return [
            'data' => $ijininstruktur,
            'jumlahData' => $jumlahData
        ];
    }



}
