<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DepositUang;
use App\Models\promo;
use App\Models\user;
use App\Models\member;
use App\Models\pegawai;
use Hidehalo\Nanoid\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DepositUangController extends Controller
{

    public function index(){
    //    $deposit_uang = DepositUang::select('user.ID_USER', 'member.ID_MEMBER', 'promo.ID_PROMO', 'pegawai.ID_PEGAWAI')
    //    ->join('user' , 'user.ID_USER', '=' , 'deposit_uang.MEM_ID_USER', 'left outer')
    //    ->join('member' , 'member.ID_MEMBER', '=' , 'deposit_uang.ID_MEMBER' ,'left outer')   
    //    ->join('promo' , 'promo.ID_PROMO', '=' , 'deposit_uang.ID_PROMO', 'left outer')
    //    ->join('user' , 'user.ID_USER', '=' , 'deposit_uang.PEG_ID_USER', 'left outer')
    //    ->join('pegawai' , 'pegawai.ID_USER', '=' , 'deposit_uang.ID_PEGAWAI', 'left outer')
    //    ->where('IS_DELETED_USER', NULL)
    //    ->get();

    $deposit_uang = DepositUang::get();
    return response()->json(['data' => $deposit_uang]);


       if(count($deposit_uang) > 0)
        {
            return response()->json([
                'status' => 'success',
                'message' => 'Data Deposite Uang Berhasil Ditampilkan',
                'data' => $deposit_uang
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Data Deposite Uang Kosong',
            'data' => null
        ], 404);

    }

    public function store(Request $request)
    {
        $client = new Client();
        $data = $request->json()->all();
        $promo = promo::find($data['ID_PROMO']);
        if($data['JUMLAH_DEPOSIT_UANG'] <= 500000){
            return response (['Transaksi Gagal, Minimal Deposite 500000'], 400);
        }

        if($request->ID_PROMO  != null){
            $minimal_deposit = $promo['MINIMAL_DEPOSIT_PROMO'];
            $createDepoUang['JUMLAH_DEPOSIT_UANG'] = $data['JUMLAH_DEPOSIT_UANG'];
            if($minimal_deposit <= $data['JUMLAH_DEPOSIT_UANG']){
                $createDepoUang['ID_PROMO'] =  $data['ID_PROMO'];
                $createDepoUang['BONUS_DEPOSIT_UANG'] =  $promo['BONUS_PROMO'];
                $createDepoUang['TOTAL_DEPOSIT'] =  $promo['BONUS_PROMO'] + $data['JUMLAH_DEPOSIT_UANG'];
            }else{
                $createDepoUang['ID_PROMO'] = NULL;
                $createDepoUang['BONUS_DEPOSIT_UANG'] = 0;
                $createDepoUang['TOTAL_DEPOSIT'] = $data['JUMLAH_DEPOSIT_UANG'];
            }  
        }else{
            $createDepoUang['JUMLAH_DEPOSIT_UANG'] = $data['JUMLAH_DEPOSIT_UANG'];
            $createDepoUang['BONUS_DEPOSIT_UANG'] = 0;
            $createDepoUang['TOTAL_DEPOSIT'] = $data['JUMLAH_DEPOSIT_UANG'];
        }
        $tanggalNow = Carbon::now();

        $idAwal = 1;
        $cekIdTerakhir = DB::select("SELECT NO_STRUK_DEPOSIT_UANG  FROM deposit_uang  ORDER BY NO_STRUK_DEPOSIT_UANG  desc limit 1 ");
        if ($cekIdTerakhir != null) {
            $idTerakhir = $cekIdTerakhir[0]->NO_STRUK_DEPOSIT_UANG;
            $idAwal = intval(substr($idTerakhir, 6)) + 1;
        }
        $date = Carbon::now()->format('y.m');
        $format = $date . '.' . $idAwal;

        DepositUang::create([
            'NO_STRUK_DEPOSIT_UANG' => $format,
            'MEM_ID_USER' => $request['MEM_ID_USER'],
            'ID_MEMBER' => $request['ID_MEMBER'],
            'ID_PROMO' => $request['ID_PROMO'],
            'PEG_ID_USER' => $request['PEG_ID_USER'],
            'ID_PEGAWAI' => $request['ID_PEGAWAI'],
            'JUMLAH_DEPOSIT_UANG' => $request['JUMLAH_DEPOSIT_UANG'],
            'BONUS_DEPOSIT_UANG' => $createDepoUang['BONUS_DEPOSIT_UANG'],
            'TANGGAL_DEPOSIT_UANG' => $tanggalNow,
            'TOTAL_DEPOSIT' => $request['TOTAL_DEPOSIT'],
        ]);

        $newMember = member::find($data['MEM_ID_USER']);
        $newMember->SISA_DEPOSIT_MEMBER = $data['TOTAL_DEPOSIT'] + $newMember['SISA_DEPOSIT_MEMBER'];
        $newMember->update();
    }
}
