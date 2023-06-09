<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\CarbonPeriod;
use App\Models\booking_gym;
use App\Models\booking_kelas;
use App\Models\DepositeKelas;
use App\Models\DepositUang;
use App\Models\jadwal;
use App\Models\jadwal_harian;
use App\Models\jadwal_default;
use App\Models\user;
use App\Models\kelas;
use App\Models\member;
use App\Models\pegawai;
use App\Models\instruktur;
use App\Models\membership;
use Carbon\Carbon;
use DB;


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
            ->orderBy('kelas.NAMA_KELAS', 'ASC')
            ->get();
        return $laporan;
    }


    // public function LaporanAktivasiGymBulanan(Request $request, $nowMonth)
    // {
    //     // Mengonversi bulan yang diberikan menjadi format dua digit (misalnya, 05 untuk Mei)
    //     $formattedMonth = str_pad($nowMonth, 2, '0', STR_PAD_LEFT);

    //     $booking_gym = booking_gym::join('user', 'booking_gym.ID_USER', '=', 'user.ID_USER')
    //         ->join('member', 'booking_gym.ID_MEMBER', '=', 'member.ID_MEMBER')
    //         ->whereRaw("DATE_FORMAT(booking_gym.TANGGAL_GYM, '%m') = ?", [$formattedMonth])
    //         ->select('booking_gym.*', 'user.*', 'member.*')
    //         ->orderBy('booking_gym.TANGGAL_GYM', 'ASC')
    //         ->get();
    //     return $booking_gym;
    // }


    public function LaporanAktivitasGYMBulanan2(){
        $nowDate = Carbon::now()->tz('Asia/Jakarta');
        $startOfMonth = Carbon::parse($nowDate->startOfMonth()->format('Y-m-d'));
        $endOfMonth = Carbon::parse($nowDate->endOfMonth()->format('Y-m-d'));

        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

        $Laporan = ['tanggal' => null, 'jumlah' => null, 'total' => null];
        $total = 0;

        $hadirGyms = booking_gym::whereBetween('TANGGAL_GYM', [$startOfMonth, $endOfMonth])
        ->whereNot('WAKTU_PRESENSI', null)
        ->groupBy('TANGGAL_GYM')->selectRaw('TANGGAL_GYM, count(*) as jumlah')
        ->get();


        foreach ($period as $index => $date) {
            $dateGym = $date->format('Y-m-d');
            $date = $date->format('d M Y');

            $Laporan['tanggal'][$index] = $date;
            $Laporan['jumlah'][$index] = 0;

            $jumlahHadir = $hadirGyms->where('TANGGAL_GYM', $dateGym)->first();
            if ($jumlahHadir) {
                $Laporan['jumlah'][$index] = $jumlahHadir->jumlah;
                $total += $jumlahHadir->jumlah;
            }
        }
        
        $Laporan['total'] = $total;

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Tampil Laporan Aktivitas GYM Bulanan',
            'data' => $Laporan,
        ], 200);

    }



    public function LaporanInstruktur()
    {
        $data = DB::table('jadwal')
            ->join('instruktur', 'jadwal.ID_INSTRUKTUR', '=', 'instruktur.ID_INSTRUKTUR')
            ->join('user', 'user.ID_USER', '=', 'instruktur.ID_USER')
            ->select('jadwal.*', 'user.*', 'instruktur.KETERLAMBATAN_INSTRUKTUR')
            ->orderBy('instruktur.KETERLAMBATAN_INSTRUKTUR', 'desc')
            ->get();

            // dd($data);
        foreach ($data as $item) {
            $item->jumlahHadir = DB::table('jadwal_harian')
                ->where('jadwal_harian.ID_JADWAL', '=', $item->ID_JADWAL)
                ->whereNotNull('WAKTU_MULAI')
                ->count();
        }
        foreach ($data as $item) {
            $item->jumlahLibur = DB::table('jadwal_harian')
                ->whereNull('WAKTU_MULAI')
                ->where('ID_JADWAL', $item->ID_JADWAL)
                ->count();
        }
        $responseData = [
            'data' => $data,
            'bulan' => date('F'),
            'tahun' => date('Y'),
            'tanggalCetak' => date('Y-m-d')
        ];
        return response()->json([
            'data' => $responseData
        ]);
    }
   


    public function LaporanPendapatan(Request $request){
        $Year = 2023;

        $monthlySums = ['Aktivasi' => null, 'Deposit' => null, 'Total' => null, 'TotalSemua' => 0];

        for ($month = 1; $month <= 12; $month++) {
            $paddedMonth = str_pad($month, 2, '0', STR_PAD_LEFT);
            
            if($month > 9){
                $sumTA = membership::whereYear('TANGGAL_AKTIVASI_MEMBERSHIP', $Year)
                    ->whereMonth('TANGGAL_AKTIVASI_MEMBERSHIP', $month)
                    ->sum('TOTAL_HARGA_AKTIVASI');
                $sumTDK = DepositeKelas::whereYear('TANGGAL_DEPOSIT_KELAS', $Year)
                    ->whereMonth('TANGGAL_DEPOSIT_KELAS', $month)
                    ->sum('TOTAL_HARGA_DEPOSITE_KELAS');
                $sumTDU = DepositUang::whereYear('TANGGAL_DEPOSIT_UANG', $Year)
                    ->whereMonth('TANGGAL_DEPOSIT_UANG', $month)
                    ->sum('JUMLAH_DEPOSIT_UANG');
            }else{
                $sumTA = membership::whereYear('TANGGAL_AKTIVASI_MEMBERSHIP', $Year)
                    ->whereMonth('TANGGAL_AKTIVASI_MEMBERSHIP', $paddedMonth)
                    ->sum('TOTAL_HARGA_AKTIVASI');
                $sumTDK = DepositeKelas::whereYear('TANGGAL_DEPOSIT_KELAS', $Year)
                    ->whereMonth('TANGGAL_DEPOSIT_KELAS', $month)
                    ->sum('TOTAL_HARGA_DEPOSITE_KELAS');
                $sumTDU = DepositUang::whereYear('TANGGAL_DEPOSIT_UANG', $Year)
                    ->whereMonth('TANGGAL_DEPOSIT_UANG', $month)
                    ->sum('JUMLAH_DEPOSIT_UANG');
            }

            $monthlySums['Aktivasi'][$month] = $sumTA;
            $monthlySums['Deposit'][$month] = $sumTDK + $sumTDU;
            $monthlySums['Total'][$month] = $sumTA + $sumTDK + $sumTDU;
            $monthlySums['TotalSemua'] = $monthlySums['TotalSemua'] + $monthlySums['Total'][$month];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Tampil Laporan Pendapatan',
            'data' => $monthlySums,
        ], 200);
    }



}
