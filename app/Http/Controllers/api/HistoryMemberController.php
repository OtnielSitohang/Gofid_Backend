<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\booking_gym;
use App\Models\booking_kelas;
use App\Models\jadwal;
use App\Models\user;
use App\Models\member;

class HistoryMemberController extends Controller
{
    public function indexHistoryMemberGym(Request $request, $ID_MEMBER)
    {
        $bookingGyms = booking_gym::with(['User', 'MEMBER'])
            ->where('ID_MEMBER', $ID_MEMBER)
            ->orderBy('TANGGAL_BOOKING_GYM', 'asc')
            ->orderBy('TANGGAL_GYM', 'asc')
            ->get();

        return response()->json([
            'message' => 'Success Tampil Data',
            'data' => $bookingGyms,
        ], 200);
    }


public function indexHistoryMemberKelas(Request $request, $ID_MEMBER)
    {
        $booking_kelas = booking_kelas::with(['Instruktur', 'MEMBER', 'JADWAL.kelas'])
        ->where('ID_MEMBER', $ID_MEMBER)
        ->orderBy('TANGGAL_BOOKING_KELAS', 'asc')
        ->orderBy('TANGGAL_KELAS', 'asc')
        ->get();


        $data = $booking_kelas->map(function ($item) {
            // $kelas = $item->JADWAL->kelas;
            $jadwal = $item->JADWAL;
            // dd($item->JADWAL->kelas);

            return [
                'ID_BOOKING_KELAS' => $item->ID_BOOKING_KELAS,
                'ID_JADWAL' => $item->ID_JADWAL,
                'ID_USER' => $item->ID_USER,
                'ID_MEMBER' => $item->ID_MEMBER,
                'NO_STRUK_PRESENSI_KELAS' => $item->NO_STRUK_PRESENSI_KELAS,
                'TANGGAL_KELAS' => $item->TANGGAL_KELAS,
                'STATUS_PRESENSI' => $item->STATUS_PRESENSI,
                'IS_CANCELED' => $item->IS_CANCELED,
                'SESI_BOOKING_KELAS' => $item->SESI_BOOKING_KELAS,
                'TANGGAL_BOOKING_KELAS' => $item->TANGGAL_BOOKING_KELAS,
                'IS_DELETED_BOOKING_KELAS' => $item->IS_DELETED_BOOKING_KELAS,
                'Instruktur' => $item->Instruktur,
                'm_e_m_b_e_r' => $item->MEMBER,
                'jadwal' => $jadwal,
                // 'nama_kelas' => $kelas,
            ];
        });

    
    return response()->json([
        'message' => 'Success Tampil Data',
        'data' => $data,
    ], 200);
    




    }

}
