<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\promo;

class PromoController extends Controller
{
    public function index(){
        $promo = promo::get();
        return response()->json(['data' => $promo]);
    }
}
