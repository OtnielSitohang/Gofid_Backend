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
        $start_date = Carbon::now()->setTimezone('Asia/Jakarta')->startOfWeek(Carbon::SUNDAY);
        $end_date = Carbon::now()->setTimezone('Asia/Jakarta')->endOfWeek(Carbon::SATURDAY);


        $jadwal_harian = jadwal_harian::join('jadwal', 'jadwal_harian.ID_JADWAL', '=' , 'jadwal.ID_JADWAL')
            ->join('user' , 'user.ID_USER', '=' , 'jadwal.ID_USER')
            ->join('kelas' , 'kelas.ID_KELAS', '=' , 'jadwal.ID_KELAS')
            ->where('IS_DELETED_JADWAL', NULL)
            ->where('jadwal_harian.TANGGAL_JADWAL_HARIAN', '>=', $start_date)
            ->where('jadwal_harian.TANGGAL_JADWAL_HARIAN', '<=', $end_date)
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
        $cekJadwalHarian = jadwal_harian::where('TANGGAL_JADWAL_HARIAN', '>', Carbon::now()->setTimezone('Asia/Jakarta')->startOfWeek(Carbon::SUNDAY)->format('Y-m-d H:i:s'))->first();
        // dd($cekJadwalHarian);

        if (!is_null($cekJadwalHarian)) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal Harian telah digenerate',
                'data' => null
            ]);
        }

        // Generate jadwal
        $start_date = Carbon::now()->setTimezone('Asia/Jakarta')->startOfWeek(Carbon::SUNDAY);
        $end_date = Carbon::now()->setTimezone('Asia/Jakarta')->endOfWeek(Carbon::SATURDAY);


        // Mapping Hari
        $map = [
            'monday' => 0,
            'tuesday' => 1,
            'wednesday' => 2,
            'thursday' => 3,
            'friday' => 4,
            'saturday' => 5,
            'sunday' => 6,
        ];

        for ($date = $start_date; $date->lte($end_date); $date->addDay()) {
            $client = new Client();
            $ID_JADWAL = $client->generateId($size = 21);

            $hari = Carbon::parse($date)->format('l');
            $jadwal_default = DB::table('jadwal_default')
                ->where('jadwal_default.HARI_JADWAL_DEFAULT', '=', $map[strtolower($hari)])
                ->get();

            foreach ($jadwal_default as $jd) {
                // Cek apakah jadwal sudah digenerate pada tanggal tersebut
                $jadwal_harian = DB::table('jadwal_harian')
                    ->where('TANGGAL_JADWAL_HARIAN', '=', $date->toDateString())
                    ->where('ID_JADWAL', '=', $jd->ID_JADWAL)
                    ->first();

                if (!$jadwal_harian) {
                    DB::table('jadwal_harian')->insert([
                        'ID_JADWAL_HARIAN' => $ID_JADWAL,
                        'HARI_JADWAL_HARIAN' => $jd->HARI_JADWAL_DEFAULT,
                        'TANGGAL_JADWAL_HARIAN' => $date->toDateString(),
                        'status' => 0,
                        'SLOT_KELAS' => 10,
                        'ID_JADWAL' => $jd->ID_JADWAL,   
                    ]);
                }
            }
        }


    }


    public function GetJadwalByIns(Request $request, $ID_INSTRUKTUR){
        $startDate = Carbon::now('Asia/Jakarta')->startOfWeek();
        $endDate = Carbon::now('Asia/Jakarta')->endOfWeek()->subDay();

        $formattedStartDate = $startDate->format('Y-m-d 00:00:00');
        $formattedEndDate = $endDate->format('Y-m-d 23:59:59');

        // dd($formattedStartDate);

        $jadwal_harian = jadwal_harian::join('jadwal', 'jadwal_harian.ID_JADWAL', '=' , 'jadwal.ID_JADWAL')
            ->join('user' , 'user.ID_USER', '=' , 'jadwal.ID_USER')
            ->join('kelas' , 'kelas.ID_KELAS', '=' , 'jadwal.ID_KELAS')
            ->where('IS_DELETED_JADWAL', NULL)
            ->whereBetween('TANGGAL_JADWAL_HARIAN', [$formattedStartDate, $formattedEndDate])
            ->where(function ($query) use ($ID_INSTRUKTUR) {
                $query->where('ID_INSTRUKTUR', $ID_INSTRUKTUR)
                    ->orWhere('ID_INSTRUKTUR_PENGGANTI', $ID_INSTRUKTUR);
            })
            ->get();

            $jumlahData = $jadwal_harian->count();
        if(count($jadwal_harian) > 0)
        {
            return response()->json([
                'status' => 'success',
                'message' => 'Data Jadwal Berhasil Ditampilkan',
                'data' => $jadwal_harian,
                'Jumlah Data' => $jumlahData
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Data Jadwal Kosong',
            'data' => null
        ], 404);
    }


    public function cekInstrukturPengganti(Request $request,  $ID_INSTRUKTUR) {
        $instrukturKosong = jadwal::join('instruktur', 'jadwal.ID_INSTRUKTUR', '=', 'instruktur.ID_INSTRUKTUR')
        ->join('user' , 'user.ID_USER' , '=' , 'jadwal.ID_USER')
        ->join('jadwal_harian' , 'jadwal_harian.ID_JADWAL' , '=' , 'jadwal.ID_JADWAL')
            ->where('jadwal.ID_INSTRUKTUR', '!=', $ID_INSTRUKTUR)
            ->where('HARI_JADWAL_HARIAN', '!=' ,  $request->hariJadwal)
            ->where('SESI_JADWAL', '!=', $request->sesiJadwal)
            ->get(['instruktur.ID_INSTRUKTUR', 'instruktur.ID_USER', 'user.NAMA_USER']);
    
        return $instrukturKosong;
    }


    public function getDataHariini(){
        date_default_timezone_set('Asia/Jakarta');
        $start_date = Carbon::now()->format('Y-m-d');
        // dd($start_date);
        $jadwal_harian = jadwal_harian::join('jadwal', 'jadwal_harian.ID_JADWAL', '=' , 'jadwal.ID_JADWAL')
            ->join('user' , 'user.ID_USER', '=' , 'jadwal.ID_USER')
            ->join('kelas' , 'kelas.ID_KELAS', '=' , 'jadwal.ID_KELAS')
            ->where('IS_DELETED_JADWAL', NULL)
            ->where('jadwal_harian.TANGGAL_JADWAL_HARIAN', '=', $start_date)
            ->get();

        // dd($start_date);
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


    public function UpdateJadwalMulai($ID_JADWA_HARIAN)
    {
        date_default_timezone_set('Asia/Jakarta');
        try {
            $DataJadwal = jadwal_harian::findOrFail($ID_JADWA_HARIAN);
            $JamSekarang = date('H:i:s');

            $DataJadwal->WAKTU_MULAI = $JamSekarang;
            $DataJadwal->update();

            return response()->json([
                'status' => 'success',
                'message' => 'Data Jadwal Berhasil Ditampilkan',
                'data' => $DataJadwal
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui data jadwal.'
            ], 500);
        }
    }
    public function UpdateJadwalSelesai($ID_JADWA_HARIAN)
    {
        date_default_timezone_set('Asia/Jakarta');
        try {
            $DataJadwal = jadwal_harian::findOrFail($ID_JADWA_HARIAN);
            $JamSekarang = date('H:i:s');

            $DataJadwal->WAKTU_SELESAI = $JamSekarang;
            $DataJadwal->update();

            return response()->json([
                'status' => 'success',
                'message' => 'Data Jadwal Berhasil Ditampilkan',
                'data' => $DataJadwal
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui data jadwal.'
            ], 500);
        }
    }

    public function GetDataInsToday($ID_INSTRUKTUR){
        $now = Carbon::now('Asia/Jakarta');
        $formattedDate = $now->format('Y-m-d');
        $jadwalToday = jadwal_harian::join('jadwal', 'jadwal_harian.ID_JADWAL', '=' , 'jadwal.ID_JADWAL')
            ->join('user' , 'user.ID_USER', '=' , 'jadwal.ID_USER')
            ->join('kelas' , 'kelas.ID_KELAS', '=' , 'jadwal.ID_KELAS')
            ->where('IS_DELETED_JADWAL', NULL)
            ->where('TANGGAL_JADWAL_HARIAN', $formattedDate)
            ->where('jadwal.ID_INSTRUKTUR', $ID_INSTRUKTUR)
            ->get();

            $jumlahData = $jadwalToday->count();

        if(count($jadwalToday) > 0)
        {
            return response()->json([
                'status' => 'success',
                'message' => 'Data Jadwal Berhasil Ditampilkan',
                'data' => $jadwalToday,
                'Jumlah Data' => $jumlahData
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Data Jadwal Kosong',
            'data' => null
        ], 404);
    }

    

}
