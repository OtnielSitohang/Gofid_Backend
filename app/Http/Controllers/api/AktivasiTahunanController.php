<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AktivasiTahunan;
use App\Models\user;
use App\Models\member;
use App\Models\pegawai;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Hidehalo\Nanoid\Client;


class AktivasiTahunanController extends Controller
{

    public function index(){
        $aktivasiTahunan = AktivasiTahunan::get();
        return response ()-> json(['data' => $aktivasiTahunan]);
    }


    public function store(Request $request){
        $client = new Client();
        $data = $request->json()->all();

        $tanggalNow = Carbon::now();
        $tanggalEnd = $tanggalNow->copy()->addDays(365);
        $ID_MEMBERSHIP = $client->generateId($size = 21);


        $idAwalStruck = 1;
        $CekIDTerakhirStruck = DB::select("SELECT NO_STRUK_MEMBERSHIP  FROM membership  ORDER BY NO_STRUK_MEMBERSHIP  desc limit 1 ");
        if ($CekIDTerakhirStruck != null) {
            $idTerakhir = $CekIDTerakhirStruck[0]->NO_STRUK_MEMBERSHIP;
            $idAwalStruck = intval(substr($idTerakhir, 6)) + 1;
        }
        $date = Carbon::now()->format('y.m');
        $formatIDStruck = $date . '.' . $idAwalStruck;

        $idAwalMembership = 1;
        $CekIDTerakhirIDMembership= DB::select("SELECT ID_MEMBERSHIP FROM membership ORDER BY NO_STRUK_MEMBERSHIP desc limit 1 ");
        if($CekIDTerakhirIDMembership != null){
            $lastid = $CekIDTerakhirIDMembership[0]->ID_MEMBERSHIP;
            $idAwalMembership = intval(substr($lastid, 6)) + 1;
        }

        $formatIDMembership = $date. '.' . $idAwalMembership;



        AktivasiTahunan::create([
            'ID_MEMBERSHIP' =>$formatIDMembership,
            'NO_STRUK_MEMBERSHIP' => $formatIDStruck,
            'MEM_ID_USER' => $request['MEM_ID_USER'],
            'ID_MEMBER' => $request['ID_MEMBER'],
            'PEG_ID_USER' => $request['PEG_ID_USER'],
            'ID_PEGAWAI' => $request['ID_PEGAWAI'],
            'TANGGAL_AKTIVASI_MEMBERSHIP' => $tanggalNow,
            'TANGGAL_KADALUARSA_MEMBERSHIP' => $tanggalEnd,
        ]);

        return response()->json('Data Berhasil Ditambah');
    }

}
