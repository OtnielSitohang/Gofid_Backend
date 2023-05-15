<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ijininstruktur;
use App\Models\user;
use App\Models\member;
use App\Models\pegawai;
use App\Models\instruktur;
use App\Models\jadwal;
use App\Models\kelas;
use Hidehalo\Nanoid\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class IjinInstrukturController extends Controller
{
    public function index(){

        $ijininstruktur = IjinInstruktur::with(['instruktur', 'InstrukturUserName' , 'InstrukturPengganti' , 'InstrukturPenggantiUserName'])->get();

        return response([
            'message'=>'Success Tampil Data',
            'data' => $ijininstruktur
        ],200); 
    }
    public function update(Request $request, $ID_IJIN_INSTRUKTUR){

        $input = $request->all();
        $newIzinInstruktur = ijininstruktur::find($ID_IJIN_INSTRUKTUR);
        // dd($input);

        $newIzinInstruktur->STATUS_IZIN = $request->input('STATUS_IZIN');
        // dd($newIzinInstruktur);
        
        $newIzinInstruktur->update();
        return response()->json($newIzinInstruktur);

    }
}
