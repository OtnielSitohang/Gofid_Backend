<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\booking_gym;
use App\Models\booking_kelas;
use App\Models\ijininstruktur;
use App\Models\jadwal;
use App\Models\jadwal_harian;
use App\Models\user;
use App\Models\member;

class HistoryInstrukturController extends Controller
{
    public function indexHistoryInstruktur(Request $request, $ID_INSTRUKTUR)
    {
        $today = date('Y-m-d');
        $jadwal = jadwal_harian::join('jadwal', 'jadwal_harian.ID_JADWAL', '=' , 'jadwal.ID_JADWAL')
            ->join('user', 'user.ID_USER', '=', 'jadwal.ID_USER')
            ->join('kelas', 'kelas.ID_KELAS', '=', 'jadwal.ID_KELAS')
            ->where('IS_DELETED_JADWAL', NULL)
            ->where(function ($query) use ($ID_INSTRUKTUR) {
                $query->where('ID_INSTRUKTUR', $ID_INSTRUKTUR)
                    ->orWhere('ID_INSTRUKTUR_PENGGANTI', $ID_INSTRUKTUR);
            })
            ->where('TANGGAL_JADWAL_HARIAN', '<', $today)
            ->orderBy('TANGGAL_JADWAL_HARIAN', 'DESC')
            ->get();

        // $jadwal = jadwal::with(['kelas', 'jadwal_harian'])
        // ->where('ID_INSTRUKTUR', $ID_INSTRUKTUR)
        // ->get();

        $jumlahData = $jadwal->count();
        
        $ijininstruktur = IjinInstruktur::with(['instruktur', 'InstrukturUserName', 'InstrukturPengganti', 'InstrukturPenggantiUserName'])
        ->join('jadwal', 'jadwal.ID_JADWAL', '=', 'IjinInstruktur.ID_JADWAL')
        ->join('kelas', 'kelas.ID_KELAS', 'jadwal.ID_KELAS')
        ->where('IjinInstruktur.ID_INSTRUKTUR', $ID_INSTRUKTUR)
        ->get();
        $jumlahDataIzin = $ijininstruktur->count();

        return [
            'data' => $jadwal,
            'instruktur' => $ijininstruktur,
            'jumlahData' => $jumlahData,
            'jumlahDataIzin' => $jumlahDataIzin
        ];
    }

    // {
    //     $jadwal = jadwal::with(['kelas', 'jadwal_harian'])
    //     ->join('ijininstruktur' , 'ijininstruktur.ID_INSTRUKTUR' , 'jadwal.ID_INSTRUKTUR')
    //     ->where('jadwal.ID_INSTRUKTUR', $ID_INSTRUKTUR)
    //     ->get();

    //     $jumlahData = $jadwal->count();

    //     return [
    //         'data' => $jadwal,
    //         'jumlahData' => $jumlahData
    //     ];
    // }
}
