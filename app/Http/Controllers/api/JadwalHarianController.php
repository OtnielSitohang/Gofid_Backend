<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\jadwal_default;
use App\Models\jadwal;
use App\Models\jadwal_harian;
use App\Models\instruktur;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Hidehalo\Nanoid\Client;
use Illuminate\Support\Facades\Validator;


class JadwalHarianController extends Controller
{
    public function index()
    {
        $jadwal_harian = jadwal_harian::join('jadwal', 'jadwal_harian.ID_JADWAL', '=' , 'jadwal.ID_JADWAL')
        ->join('user' , 'user.ID_USER', '=' , 'jadwal.ID_USER')
        ->join('kelas' , 'kelas.ID_KELAS', '=' , 'jadwal.ID_KELAS')
        ->where('IS_DELETED_JADWAL', NULL)
        // ->where ('IS_DELETED_JADWAL_HARIAN', NULL) 
        ->get();
        
        if(count($jadwal_harian) > 0)
        {
            return response()->json([
                'status' => 'success',
                'message' => 'Data Jadwal Berhasil Ditampilkan',
                'data' => $jadwal_harian
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Data Jadwal Kosong',
            'data' => null
        ], 404);
    }

    public function updateToHoliday($id)
    {
        $newJadwalHarian = jadwal_harian::find($id);
        $newJadwalHarian['STATUS'] = true;
        $newJadwalHarian->save();
        return response()->json($newJadwalHarian);
    }


    public function storeMax(){
        // cek udah generate atau belum
        $cekJadwalHarian = jadwal_harian::where('TANGGAL_JADWAL_HARIAN', '>', Carbon::now()->startOfWeek(Carbon::SUNDAY)->format('Y-m-d'))->first();
        // dd($cekJadwalHarian);
        if(!is_null($cekJadwalHarian)){
            return response()->json([
                'success' => false,
                'message' => 'Jadwal Harian telah digenerate',
                'data' => null
            ]);
        }
        
        //generate
        $start_date = Carbon::now()->startOfWeek(Carbon::SUNDAY)->addDay();
        // $end_date = Carbon::now()->startOfWeek(Carbon::SUNDAY)->addDays(7);
        $end_date = Carbon::now()->endOfWeek(Carbon::SUNDAY);
        
        //Mapping Hari
        $map = [
            'monday' => 0,
            'tuesday' => 1,
            'wednesday' => 2,
            'thursday' => 3,
            'friday' => 4,
            'saturday' => 5,
            'sunday' => 6,
        ];
        for($date = $start_date ; $date->lte($end_date);$date->addDay())
        {
            $client = new Client();
            $ID_JADWAL = $client->generateId($size = 21);

            $hari = Carbon::parse($date)->format('l');
            $jadwal_default = DB::table('jadwal_default')
            ->where('jadwal_default.HARI_JADWAL_DEFAULT','=',$map[strtolower($hari)])
            ->get();

            foreach($jadwal_default as $jd){
                //Agar tidak double
                $jadwal_harian = DB::table('jadwal_harian')
                ->where('TANGGAL_JADWAL_HARIAN','=',$date->toDateString())
                ->where('ID_JADWAL', '=', $jd->ID_JADWAL)
                ->first();
                if(!$jadwal_harian){
                    DB::table('jadwal_harian')->insert([
                        'ID_JADWAL_HARIAN' => $ID_JADWAL,
                        'HARI_JADWAL_HARIAN' => $jd->HARI_JADWAL_DEFAULT,
                        'TANGGAL_JADWAL_HARIAN' =>$date->toDateString(),
                        'status' => 0,
                        'ID_JADWAL' =>$jd->ID_JADWAL,   
                    ]);
                }
            }
        }

    }


}
