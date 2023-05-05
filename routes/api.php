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
Route::delete('/jadwal_default/destroy/{ID_JADWAL}', [App\Http\Controllers\api\jadwalDefaultController::class, 'destroy']);
Route::post('/jadwal_default/create', [App\Http\Controllers\api\jadwalDefaultController::class, 'store']);
Route::put('/jadwal_default/{ID_USER}', [App\Http\Controllers\api\jadwalDefaultController::class, 'update']);


//Kelas
Route::get('/kelas', [App\Http\Controllers\api\KelasController::class, 'index']);
