<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DepositeKelas;
use App\Models\promo;
use App\Models\user;
use App\Models\member;
use App\Models\pegawai;
use App\Models\kelas;
use Hidehalo\Nanoid\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DepositeKelasController extends Controller
{
    public function store(Request $request)
    {
        $client = new Client();
        $data = $request->json()->all();
        $promo = promo::find($data['ID_PROMO']);
        // if($data['JUMLAH_DEPOSIT_KELAS'] <= 500000){
        //     return response (['Transaksi Gagal, Minimal Deposite 500000'], 400);
        // }

        if($request->ID_PROMO  != null){
            $minimal_deposit = $promo['MINIMAL_DEPOSIT_PROMO'];
            $createDepoUang['JUMLAH_DEPOSIT_KELAS'] = $data['JUMLAH_DEPOSIT_KELAS'];
            if($minimal_deposit <= $data['JUMLAH_DEPOSIT_KELAS']){
                $createDepoUang['ID_PROMO'] =  $data['ID_PROMO'];
                $createDepoUang['BONUS_DEPOSIT_KELAS'] =  $promo['BONUS_PROMO'];
                $createDepoUang['TOTAL_DEPOSIT'] =  $promo['BONUS_PROMO'] + $data['JUMLAH_DEPOSIT_KELAS'];
            }else{
                $createDepoUang['ID_PROMO'] = NULL;
                $createDepoUang['BONUS_DEPOSIT_KELAS'] = 0;
                $createDepoUang['TOTAL_DEPOSIT'] = $data['JUMLAH_DEPOSIT_KELAS'];
            }  
        }else{
            $createDepoUang['JUMLAH_DEPOSIT_KELAS'] = $data['JUMLAH_DEPOSIT_KELAS'];
            $createDepoUang['BONUS_DEPOSIT_KELAS'] = 0;
            $createDepoUang['TOTAL_DEPOSIT'] = $data['JUMLAH_DEPOSIT_KELAS'];
        }
        $tanggalNow = Carbon::now();

        $idAwal = 1;
        $cekIdTerakhir = DB::select("SELECT NO_STRUK_DEPOSIT_KELAS  FROM deposit_kelas  ORDER BY NO_STRUK_DEPOSIT_KELAS  desc limit 1 ");
        if ($cekIdTerakhir != null) {
            $idTerakhir = $cekIdTerakhir[0]->NO_STRUK_DEPOSIT_KELAS;
            $idAwal = intval(substr($idTerakhir, 6)) + 1;
        }


        if($promo['MINIMAL_DEPOSIT_PROMO'] == 5){
            $TANGGAL_KADALUARSA_DEPOSIT_KEL = Carbon::now()->addDays(30);
        }else{
            $TANGGAL_KADALUARSA_DEPOSIT_KEL = Carbon::now()->addDays(60);
        }
        $formatTANGGAL_KADALUARSA_DEPOSIT_KEL = $TANGGAL_KADALUARSA_DEPOSIT_KEL;

        $date = Carbon::now()->format('y.m');
        $format = $date . '.' . $idAwal;

        DepositeKelas::create([
            'NO_STRUK_DEPOSIT_KELAS' => $format,
            'MEM_ID_USER' => $request['MEM_ID_USER'],
            'ID_MEMBER' => $request['ID_MEMBER'],
            'ID_KELAS' => $request['ID_KELAS'],
            'ID_PROMO' => $request['ID_PROMO'],
            'PEG_ID_USER' => $request['PEG_ID_USER'],
            'ID_PEGAWAI' => $request['ID_PEGAWAI'],
            'TANGGAL_DEPOSIT_KELAS' => $tanggalNow,
            'TANGGAL_KADALUARSA_DEPOSIT_KEL' => $formatTANGGAL_KADALUARSA_DEPOSIT_KEL,
            'JUMLAH_DEPOSIT_KELAS' => $request['JUMLAH_DEPOSIT_KELAS'],
            'BONUS_DEPOSIT_KELAS' => $createDepoUang['BONUS_DEPOSIT_KELAS'],
        ]);
        
        return response()->json('Data Berhasil Ditambah');
    }

    public function index(){
        $DepositeKelas = DepositeKelas::join("kelas", "deposit_kelas.ID_KELAS" , "=" , "kelas.ID_KELAS")
        ->select("deposit_kelas.*" , 'kelas.NAMA_KELAS')
        ->get();
        return response()->json(['data' => $DepositeKelas]);
           if(count($DepositeKelas) > 0)
            {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data Deposite Uang Berhasil Ditampilkan',
                    'data' => $DepositeKelas
                ], 200);
            }
    
            return response()->json([
                'status' => 'error',
                'message' => 'Data Deposite Uang Kosong',
                'data' => null
            ], 404);
    
        }
}
