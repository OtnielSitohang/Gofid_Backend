<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use App\Models\Membership;
Use App\Models\User;
Use App\Models\Pegawai;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MembershipController extends Controller
{
    public function index()
    {
        $membership = Membership::select('user.ID_USER', 'member.ID_MEMBER', 'pegawai.ID_PEGAWAI', 'user.NAMA_USER')
        ->join('user', 'membership.MEM_ID_USER', '=', 'user.ID_USER')
        ->join('member', 'membership.ID_MEMBER', '=' , 'member.ID_MEMBER')
        ->join('user', 'membership.PEG_ID_USER', '=', 'user.ID_USER')
        ->join('pegawai', 'membership.ID_PEGAWAI', '=' , 'pegawai.ID_PEGAWAI')
        ->where('IS_DELETED_MEMBERSHIP', NULL)
        ->where ('IS_DELETED_USER', NULL) 
        ->get();
        
        if(count($membership) > 0)
        {
            return response()->json([
                'status' => 'success',
                'message' => 'Data Instruktur Berhasil Ditampilkan',
                'data' => $membership
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Data Instruktur Kosong',
            'data' => null
        ], 404);

    }
}
