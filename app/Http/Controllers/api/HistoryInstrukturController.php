<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\booking_gym;
use App\Models\booking_kelas;
use App\Models\jadwal;
use App\Models\jadwal_harian;
use App\Models\user;
use App\Models\member;

class HistoryInstrukturController extends Controller
{
    public function indexHistoryInstruktur(Request $request, $ID_INSTRUKTUR)
    {
        $jadwal = jadwal::with(['kelas', 'jadwal_harian'])
        ->where('ID_INSTRUKTUR', $ID_INSTRUKTUR)
        ->get();

        return response()->json([
            'message' => 'Success Tampil Data',
            'jadwal' => $jadwal
        ], 200);
    }
}
