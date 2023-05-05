<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\jadwal;

class jadwalController extends Controller
{
    public function index()
    {
        $jadwal = jadwal::get();
        return response ()-> json(['data' => $jadwal]);
    }
}
