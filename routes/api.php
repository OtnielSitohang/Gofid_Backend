<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//User
Route::get('/user', [App\Http\Controllers\api\UserController::class, 'index']);

//Pegawai
Route::get('/pegawai', 'App\Http\Controllers\api\PegawaiController@index');


//Promo
Route::get('/promo', [App\Http\Controllers\api\PromoController::class, 'index']);



//Instruktur
Route::get('/instruktur', [App\Http\Controllers\api\InstrukturController::class, 'index']);
Route::post('/instruktur/create', [App\Http\Controllers\api\InstrukturController::class, 'store']);
//Route::put('/instruktur/update/{id}', [App\Http\Controllers\api\InstrukturController::class, 'update']);
Route::delete('/instruktur/destroy/{ID_USER}', [App\Http\Controllers\api\InstrukturController::class, 'destroy']);
Route::put('/instruktur/update/{ID_USER}', [App\Http\Controllers\api\InstrukturController::class, 'update']);

//Member
Route::get('/member', [App\Http\Controllers\api\MemberController::class, 'index']);
Route::delete('/member/destroy/{ID_USER}', [App\Http\Controllers\api\MemberController::class, 'destroy']);
Route::post('/member/create', [App\Http\Controllers\api\MemberController::class, 'store']);
Route::put('/member/update/{ID_USER}', [App\Http\Controllers\api\MemberController::class, 'update']);


//Login Dan Update Password
Route::post('/login', [App\Http\Controllers\Api\UserController::class, 'login']);
Route::put('/updatePassword/{ID_USER}', [App\Http\Controllers\Api\UserController::class, 'updatePassword']);
// Route::post('/PegawaiController', [App\Http\Controllers\Api\PegawaiController::class, 'updatePassword']);


//Jadwal
Route::get('/jadwal', [App\Http\Controllers\api\jadwalController::class, 'index']);
// Route::delete('/jadwal/destroy/{ID_USER}', [App\Http\Controllers\api\MemberController::class, 'destroy']);
// Route::post('/member/create', [App\Http\Controllers\api\MemberController::class, 'store']);
// Route::put('/member/update/{ID_USER}', [App\Http\Controllers\api\MemberController::class, 'update']);

//Jadwal Default
Route::get('/jadwal_default', [App\Http\Controllers\api\jadwalDefaultController::class, 'index']);
Route::get('/getJadwalMobile', [App\Http\Controllers\api\jadwalDefaultController::class, 'getJadwalMobile']);
Route::delete('/jadwal_default/destroy/{ID_JADWAL}', [App\Http\Controllers\api\jadwalDefaultController::class, 'destroy']);
Route::post('/jadwal_default/create', [App\Http\Controllers\api\jadwalDefaultController::class, 'store']);
Route::put('/jadwal_default/update/{ID_JADWAL}', [App\Http\Controllers\api\jadwalDefaultController::class, 'update']);

//Jadwal Harian
Route::get('/jadwalHarian', [App\Http\Controllers\api\JadwalHarianController::class, 'index']);
Route::put('jadwalHarian/update/{ID_JADWAL}', [App\Http\Controllers\api\JadwalHarianController::class, 'updateToHoliday']);
Route::post('/jadwalHariangenerate', [App\Http\Controllers\api\JadwalHarianController::class, 'storeMax']);


//Kelas
Route::get('/kelas', [App\Http\Controllers\api\KelasController::class, 'index']);


//Aktivasi Tahunan
Route::get('/aktivasi', [App\Http\Controllers\api\AktivasiTahunanController::class, 'index']);
Route::post('/aktivasi/create', [App\Http\Controllers\api\AktivasiTahunanController::class, 'store']);

//Membership
Route::get('/membership', [App\Http\Controllers\api\MembershipController::class, 'index']);


//Transakti Deposite Uang
Route::post('/depositeUang', [App\Http\Controllers\api\DepositUangController::class, 'store']);
Route::get('/indexDepositeUang', [App\Http\Controllers\api\DepositUangController::class, 'index']);


//Transakti Deposite Kelas
Route::post('/depositeKelas/store', [App\Http\Controllers\api\DepositeKelasController::class, 'store']);
Route::get('/indexDepositeKelas', [App\Http\Controllers\api\DepositeKelasController::class, 'index']);

//TiZIN INTRUKTUR
Route::get('/ijininstruktur', [App\Http\Controllers\api\IjinInstrukturController::class, 'index']);
Route::put('/ijininstruktur/{ID_JADWAL}', [App\Http\Controllers\api\IjinInstrukturController::class, 'update']);
Route::post('/ijininstruktur/create', [App\Http\Controllers\api\IjinInstrukturController::class, 'store']);



//Sistem Kontroller
Route::put('/MendeaktifasiMember/{ID_USER}', [App\Http\Controllers\api\SistemController::class, 'MendeaktifasiMember']);
Route::put('/ResetDepositeKelas/{ID_USER}', [App\Http\Controllers\api\SistemController::class, 'ResetDepositeKelas']);
Route::get('/indexMendeaktifasiMember', [App\Http\Controllers\api\SistemController::class, 'indexMendeaktifasiMember']);
Route::get('/indexResetDepositeKelas', [App\Http\Controllers\api\SistemController::class, 'indexResetDepositeKelas']);


// Route::post('/loginMobile', [App\Http\Controllers\Api\LoginMobileController::class, 'login']);
Route::post('/loginMobile2', [App\Http\Controllers\Api\LoginMobileController::class, 'login2']);


//Presensi Member Gym
Route::get('/presensiMemberGym', [App\Http\Controllers\Api\BookingGymController::class, 'index']);
Route::put('/presensiMemberGym/{ID_USER}', [App\Http\Controllers\Api\BookingGymController::class, 'PresensiGym']);
Route::put('/cancelBookingGym/{ID_BOOKING_PRESENSI_GYM}', [App\Http\Controllers\Api\BookingGymController::class, 'cancelBookingGym']);


//Presensi Kelas Reguler
Route::get('/presensiKelas', [App\Http\Controllers\Api\BookingKelasController::class, 'index']);
Route::put('/presensiKelas/{ID_BOOKING_KELAS}', [App\Http\Controllers\Api\BookingKelasController::class, 'PresensiKelas']);


//Presensi Kelas Paket
Route::get('/presensiKelasPaket', [App\Http\Controllers\Api\BookingKelasRegulerController::class, 'indexDepositeKelas']);
Route::put('/presensiKelasPaket/{ID_BOOKING_KELAS}', [App\Http\Controllers\Api\BookingKelasRegulerController::class, 'PresensiKelas']);


//HisotryMember
Route::get('/indexHistoryMemberGym/{ID_MEMBER}', [App\Http\Controllers\Api\HistoryMemberController::class, 'indexHistoryMemberGym']);
Route::get('/indexHistoryMemberKelas/{ID_MEMBER}', [App\Http\Controllers\Api\HistoryMemberController::class, 'indexHistoryMemberKelas']);
// Route::put('/presensiKelasPaket/{ID_BOOKING_KELAS}', [App\Http\Controllers\Api\HistoryMemberController::class, 'PresensiKelas']);


//HisotryInstruktur
Route::get('/indexHistoryInstruktur/{ID_INSTRUKTUR}', [App\Http\Controllers\Api\HistoryInstrukturController::class, 'indexHistoryInstruktur']);


//LaporanAktivasiKelasBulanan 
Route::get('/LaporanAktivasiKelasBulanan/{ID_INSTRUKTUR}', [App\Http\Controllers\Api\LaporanController::class, 'LaporanAktivasiKelasBulanan']);
