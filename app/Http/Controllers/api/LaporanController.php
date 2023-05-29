<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\CarbonPeriod;
use App\Models\booking_gym;
use App\Models\booking_kelas;
use App\Models\jadwal;
use App\Models\jadwal_harian;
use App\Models\jadwal_default;
use App\Models\user;
use App\Models\kelas;
use App\Models\member;
use App\Models\pegawai;
use App\Models\instruktur;
use Carbon\Carbon;


class LaporanController extends Controller
{
    public function LaporanAktivasiKelasBulanan(Request $request, $nowMonth)
    {
        // Mengonversi bulan yang diberikan menjadi format dua digit (misalnya, 05 untuk Mei)
        $formattedMonth = str_pad($nowMonth, 2, '0', STR_PAD_LEFT);

        $laporan = Jadwal::join('jadwal_default', 'jadwal.ID_JADWAL', '=', 'jadwal_default.ID_JADWAL')
            ->join('jadwal_harian', 'jadwal.ID_JADWAL', '=', 'jadwal_harian.ID_JADWAL')
            ->join('user', 'jadwal.ID_USER', '=', 'user.ID_USER')
            ->join('instruktur', 'jadwal.ID_INSTRUKTUR', '=', 'instruktur.ID_INSTRUKTUR')
            ->join('kelas', 'jadwal.ID_KELAS', '=', 'kelas.ID_KELAS')
            ->whereRaw("DATE_FORMAT(jadwal_harian.TANGGAL_JADWAL_HARIAN, '%m') = ?", [$formattedMonth])
            ->select('kelas.*', 'user.*', 'instruktur.*', 'jadwal.*', 'jadwal_harian.*')
            ->orderBy('jadwal_harian.TANGGAL_JADWAL_HARIAN', 'ASC')
            ->get();

        // if ($laporan->isEmpty()) {
        //     return 'Data masih kosong';
        // }

        return $laporan;
    }



}
