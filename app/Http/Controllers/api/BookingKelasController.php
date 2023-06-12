<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\user;
use App\Models\member;
use App\Models\pegawai;
use App\Models\booking_kelas;
use App\Models\DepositeKelas;
use App\Models\instruktur;
use App\Models\jadwal;
use App\Models\jadwal_harian;
use App\Models\deposit_kelas;
use App\Models\kelas;
use Hidehalo\Nanoid\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingKelasController extends Controller
{
    public function index(){

        $bookingKelas = booking_kelas::join('member', 'member.ID_MEMBER', '=', 'booking_kelas.ID_MEMBER')
        ->join('jadwal' , 'jadwal.ID_JADWAL' , '=' , 'booking_kelas.ID_JADWAL')
        ->join('kelas' , 'kelas.ID_KELAS' , '=' , 'jadwal.ID_KELAS')
        ->join('user' , 'user.ID_USER', '=' , 'booking_kelas.ID_USER')
        ->join('instruktur' , 'instruktur.ID_INSTRUKTUR', '=' , 'jadwal.ID_INSTRUKTUR')
        ->get();

        return response([
            'message'=>'Success Tampil Data',
            'data' => $bookingKelas
        ],200); 
    }

    public function PresensiKelas($ID_BOOKING_KELAS){

        $idAwalStruck = 1;
        $CekIDTerakhirStruck = DB::select("SELECT NO_STRUK_PRESENSI_KELAS FROM booking_kelas ORDER BY NO_STRUK_PRESENSI_KELAS desc limit 1;");
        if ($CekIDTerakhirStruck != null) {
            $idTerakhir = $CekIDTerakhirStruck[0]->NO_STRUK_PRESENSI_KELAS;
            $idAwalStruck = intval(substr($idTerakhir, 6)) + 1;
        }
        $date = Carbon::now()->format('y.m');
        $formatNoStruckPresensi = $date . '.' . $idAwalStruck;

        $currentDate = Carbon::today();
        $newPresensiKelas = booking_kelas::find($ID_BOOKING_KELAS);


        $newPresensiKelas->STATUS_PRESENSI = 1;
        $newPresensiKelas->NO_STRUK_PRESENSI_KELAS =$formatNoStruckPresensi;
        // dd($newPresensiKelas);
        
        $newPresensiKelas->update();
        return response()->json($newPresensiKelas);
    }


    public function ShowBookingByIDMEMBER($ID_MEMBER){

        //Jika mau search semua Tanpa filter Tiap Minggu
        // $bookingKelas = booking_kelas::join('member', 'member.ID_MEMBER', '=', 'booking_kelas.ID_MEMBER')
        // ->join('jadwal', 'jadwal.ID_JADWAL', '=', 'booking_kelas.ID_JADWAL')
        // ->join('kelas', 'kelas.ID_KELAS', '=', 'jadwal.ID_KELAS')
        // ->join('user', 'user.ID_USER', '=', 'booking_kelas.ID_USER')
        // ->join('instruktur', 'instruktur.ID_INSTRUKTUR', '=', 'jadwal.ID_INSTRUKTUR')
        // ->where('booking_kelas.ID_MEMBER', '=', $ID_MEMBER)
        // ->get();

        // Get the start and end dates of the current week
        //Dengan Fillter Tiap Minggu
        $start_date = Carbon::now()->setTimezone('Asia/Jakarta')->startOfWeek(Carbon::SUNDAY);
        $end_date = Carbon::now()->setTimezone('Asia/Jakarta')->endOfWeek(Carbon::SATURDAY);

        $bookingKelas = booking_kelas::join('member', 'member.ID_MEMBER', '=', 'booking_kelas.ID_MEMBER')
        ->join('jadwal', 'jadwal.ID_JADWAL', '=', 'booking_kelas.ID_JADWAL')
        ->join('jadwal_harian', 'jadwal_harian.ID_JADWAL', '=', 'booking_kelas.ID_JADWAL')
        ->join('kelas', 'kelas.ID_KELAS', '=', 'jadwal.ID_KELAS')
        ->join('user', 'user.ID_USER', '=', 'booking_kelas.ID_USER')
        ->join('instruktur', 'instruktur.ID_INSTRUKTUR', '=', 'jadwal.ID_INSTRUKTUR')
        ->where('booking_kelas.ID_MEMBER', '=', $ID_MEMBER)
        ->where(function ($query) use ($start_date, $end_date) {
            $query->where(function ($query) use ($start_date, $end_date) {
                $query->whereBetween('jadwal_harian.TANGGAL_JADWAL_HARIAN', [$start_date, $end_date]);
            });
        })
        ->where(function ($query) use ($start_date, $end_date) {
            $query->where(function ($query) use ($start_date, $end_date) {
                $query->whereBetween('booking_kelas.TANGGAL_KELAS', [$start_date, $end_date]);
            })
            ->orWhere(function ($query) use ($start_date, $end_date) {
                $query->whereDate('booking_kelas.TANGGAL_BOOKING_KELAS', '>=', $start_date)
                    ->whereDate('booking_kelas.TANGGAL_BOOKING_KELAS', '<=', $end_date);
            });
        })
        ->whereNull('booking_kelas.IS_DELETED_BOOKING_KELAS')
        ->get();


        $jumlahData = $bookingKelas->count();

        return response([
            'message'=>'Success Tampil Data',
            'data' => $bookingKelas->toArray(),
            'Jumlah Kelas' => $jumlahData
        ],200); 
    }

    public function CancelBooking($ID_BOOKING_KELAS, $ID_JADWAL_HARIAN)
    {
        $newBookingCanceled = booking_kelas::find($ID_BOOKING_KELAS);
        $newBookingCanceled->IS_DELETED_BOOKING_KELAS = 1;

        $JadwalCanceled = jadwal_harian::find($ID_JADWAL_HARIAN);
        $JadwalCanceled->SLOT_KELAS += 1;

        $CariJadwal= jadwal::find($newBookingCanceled['ID_JADWAL']);
        $cariHargaKelas = kelas::find($CariJadwal['ID_KELAS']);
        // dd($cariHargaKelas);
        $SearchMember = member::find($newBookingCanceled['ID_MEMBER']);
        // dd($SearchMember);

        if($SearchMember['ID_KELAS'] == $cariHargaKelas['ID_KELAS'])
        {
            $SearchMember['TOTAL_KELAS'] += 1;
            $SearchMember['TEMP_PAKET'] -= 1;
        }
        else{
            $SearchMember['SISA_DEPOSIT_MEMBER'] +=  $cariHargaKelas['HARGA_KELAS'];
            $SearchMember['TEMP_UANG'] -= $cariHargaKelas['HARGA_KELAS'];
        }

        $newBookingCanceled->update();
        $JadwalCanceled->update();
        $SearchMember->update();

        return response()->json($newBookingCanceled);
    }



    public function CreateBooking(Request $request){
        $client = new Client();
        $data = $request->json()->all();
        $today = date('Y-m-d');

        //Cari Data Member yang Login
        $cekMember = member::find($request->ID_MEMBER);
        $cekHargaKelas = kelas::find($request->ID_KELAS);
        $cekSlotKelas = jadwal_harian::find($request->ID_JADWAL_HARIAN);
        // dd($cekSlotKelas);

        //Ngecek Data
        $cekData = booking_kelas::join('member', 'member.ID_MEMBER', '=', 'booking_kelas.ID_MEMBER')
        ->join('jadwal', 'jadwal.ID_JADWAL', '=', 'booking_kelas.ID_JADWAL')
        ->join('kelas', 'kelas.ID_KELAS', '=', 'jadwal.ID_KELAS')
        ->where('booking_kelas.ID_MEMBER', $request->ID_MEMBER)
        ->get();

        $data = $request->json()->all();
        $today = date('Y-m-d');

        // Find Member Data
        $cekMember = member::find($request->ID_MEMBER);
        // dd($cekMember);
        $cekHargaKelas = kelas::find($request->ID_KELAS);
        $cekSlotKelas = jadwal_harian::find($request->ID_JADWAL_HARIAN);
        // dd($cekSlotKelas);

        // Check Membership
        if ($cekMember['TANGGAL_KADALUARSA_MEMBERSHIP'] < $today) {
            return response()->json(['message' => 'Membership has expired'], 403);
        } else {
            // Check DepositePaket
            if ($request->ID_KELAS === $cekMember['ID_KELAS'] && $cekMember['TOTAL_KELAS'] != 0) {
                if ($cekSlotKelas != NULL) {
                    $newID = $this->generateBookingID();
            
                    // Create a new booking_kelas record
                    booking_kelas::create([
                        'ID_BOOKING_KELAS' => $newID,
                        'ID_JADWAL' => $request['ID_JADWAL'],
                        'ID_USER' => $request['ID_USER'], // Menggunakan 'ID_USER' dari request
                        'ID_MEMBER' => $request['ID_MEMBER'],
                        'TANGGAL_KELAS' => $request['TANGGAL_KELAS'],
                        'STATUS_PRESENSI' => 0,
                        'SESI_BOOKING_KELAS' => 0, // Set the value to 0
                        'TANGGAL_BOOKING_KELAS' => $today,
                    ],200);
            
                    $cekSlotKelas->SLOT_KELAS -= 1;
                    $cekSlotKelas->update();

                    
                    $cekMember->TOTAL_KELAS -= 1;
                    $cekMember->TEMP_PAKET += 1;
                    $cekMember->update();
                    
                    return response()->json(['message' => 'Kelas Sudah ditambahkan dengan mengurangi Paket Kelas']);
                } else {
                    return response()->json(['message' => 'Slot Kelas Habis']);
                }
            }
            elseif ($cekMember['SISA_DEPOSIT_MEMBER'] >= $cekHargaKelas['HARGA_KELAS']) {
                if ($cekSlotKelas != NULL) {
                    $newID = $this->generateBookingID();

                    // Create a new booking_kelas record
                    booking_kelas::create([
                        'ID_BOOKING_KELAS' => $newID,
                        'ID_JADWAL' => $request['ID_JADWAL'],
                        'ID_USER' => $request['ID_USER'], // Menggunakan 'ID_USER' dari request
                        'ID_MEMBER' => $request['ID_MEMBER'],
                        'TANGGAL_KELAS' => $request['TANGGAL_KELAS'],
                        'STATUS_PRESENSI' => 0,
                        'SESI_BOOKING_KELAS' => 0, // Set the value to 0
                        'TANGGAL_BOOKING_KELAS' => $today,
                    ],200);


                    $cekSlotKelas->SLOT_KELAS -= 1;
                    $cekSlotKelas->update();

                    $cekMember->SISA_DEPOSIT_MEMBER -= $cekHargaKelas['HARGA_KELAS'];
                    $cekMember->TEMP_UANG += $cekHargaKelas['HARGA_KELAS'];
                    $cekMember->update();
                    return response()->json(['message' => 'Kelas Sudah ditambahkan dengan mengurangi deposite uang']);
                } else {
                    return response()->json(['message' => 'Slot Kelas Habis']);
                }
            } else {
                return response()->json(['message' => 'Saldo Anda Tidak Cukup']);
            }
        }
    }

    private function generateBookingID()
    {
        // Dapatkan tahun dan bulan saat ini
        $year = date('y');
        $month = date('m');
        
        // Cek ID terakhir berdasarkan tahun dan bulan
        $lastID = DB::select("SELECT ID_BOOKING_KELAS FROM booking_kelas ORDER BY CAST(SUBSTRING_INDEX(ID_BOOKING_KELAS, '.', -1) AS UNSIGNED) DESC LIMIT 1; ");
        
        // Jika ID terakhir ada, ambil nomor ID dan tambahkan 1
        if (!empty($lastID)) {
            $lastNumber = intval(substr($lastID[0]->ID_BOOKING_KELAS, -2));
            $newNumber = $lastNumber + 1;
        } else {
            // Jika ID terakhir tidak ada, set nomor ID menjadi 1
            $newNumber = 1;
        }
        
        // Format ID baru dengan dua digit nomor pada akhir
        $newID = "{$year}.{$month}." . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
        
        return $newID;
    }
    

    

    public function GetJadwalHarianBelumdiBook(Request $request){
        $start_date = Carbon::now()->setTimezone('Asia/Jakarta')->startOfWeek(Carbon::SUNDAY);
        $end_date = Carbon::now()->setTimezone('Asia/Jakarta')->endOfWeek(Carbon::SATURDAY);
        $ID_JADWAL_HARIAN = $request->ID_JADWAL_HARIAN ?? []; // Mengakses nilai dan mengatur default value sebagai array kosong jika tidak ada

        if (empty($ID_JADWAL_HARIAN)) {
            $jadwal_harian = jadwal_harian::join('jadwal', 'jadwal_harian.ID_JADWAL', '=', 'jadwal.ID_JADWAL')
                ->join('user', 'user.ID_USER', '=', 'jadwal.ID_USER')
                ->join('kelas', 'kelas.ID_KELAS', '=', 'jadwal.ID_KELAS')
                ->where('IS_DELETED_JADWAL', NULL)
                ->where('jadwal_harian.TANGGAL_JADWAL_HARIAN', '>=', $start_date)
                ->where('jadwal_harian.TANGGAL_JADWAL_HARIAN', '>=', $start_date)
                ->where('jadwal_harian.SLOT_KELAS', '!=', 0)
                ->orderBy('jadwal_harian.TANGGAL_JADWAL_HARIAN')
                ->get();
        } else {
            $jadwal_harian = jadwal_harian::join('jadwal', 'jadwal_harian.ID_JADWAL', '=', 'jadwal.ID_JADWAL')
                ->join('user', 'user.ID_USER', '=', 'jadwal.ID_USER')
                ->join('kelas', 'kelas.ID_KELAS', '=', 'jadwal.ID_KELAS')
                ->where('IS_DELETED_JADWAL', NULL)
                ->where('jadwal_harian.TANGGAL_JADWAL_HARIAN', '>=', $start_date)
                ->where('jadwal_harian.TANGGAL_JADWAL_HARIAN', '>=', $start_date)
                ->whereNotIn('jadwal_harian.ID_JADWAL_HARIAN', $ID_JADWAL_HARIAN)
                ->where('jadwal_harian.SLOT_KELAS', '!=', 0)
                ->orderBy('jadwal_harian.TANGGAL_JADWAL_HARIAN')
                ->get();
        }


            $jumlahData = $jadwal_harian->count();

            return response()->json([
                'status' => 'success',
                'message' => 'Data Jadwal Berhasil Ditampilkan',
                'data' => $jadwal_harian,
                'Jumlah data' => $jumlahData

            ], 200);
    }

    // Untuk Presensi Booking Kelas
    // 1 = Hadir
    // 2 =  TIdak Hadir

    public function PresensiMemberKelasHadir($ID_JADWAL_HARIAN, $ID_MEMBER){
        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $CariJadwal = jadwal_harian::find($ID_JADWAL_HARIAN);

        $CariHargaKelas = booking_kelas::where('ID_MEMBER', $ID_MEMBER)
            ->join('jadwal', 'jadwal.ID_JADWAL', 'booking_kelas.ID_JADWAL')
            ->join('kelas', 'jadwal.ID_KELAS', 'kelas.ID_KELAS')
            ->where('booking_kelas.ID_JADWAL', $CariJadwal['ID_JADWAL'])
            ->where('TANGGAL_KELAS', $today)
            ->get();

        $findMember = member::find($ID_MEMBER);
        if ($CariHargaKelas->count() > 0) {
            $firstCariHargaKelas = $CariHargaKelas->first();

            if ($firstCariHargaKelas['ID_JADWAL'] == $findMember['ID_JADWAL']) {
                $findMember['TEMP_PAKET'] -= 1;
            } else {
                $findMember['TEMP_UANG'] -= $firstCariHargaKelas['HARGA_KELAS'];
            }
            $findMember->update();
        }
    
        $findMember->update();
        
        $result = booking_kelas::where('ID_MEMBER', $ID_MEMBER)
            ->where('ID_JADWAL', $CariJadwal['ID_JADWAL'])
            ->where('TANGGAL_KELAS', $today)
            ->update(['STATUS_PRESENSI' => 1]);
            // dd($result);

        if ($CariHargaKelas) {
            return 'Pembaruan Dibuat Menjadi Hadir deposite Telah di Potong';
        } else {
            return 'Pembaruan  Hadir gagal';
        }
    }


    public function PresensiMemberKelasTidakHadir($ID_JADWAL_HARIAN, $ID_MEMBER){
        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');

        $CariJadwal = jadwal_harian::find($ID_JADWAL_HARIAN);

        $CariHargaKelas = booking_kelas::where('ID_MEMBER', $ID_MEMBER)
            ->join('jadwal', 'jadwal.ID_JADWAL', 'booking_kelas.ID_JADWAL')
            ->join('kelas', 'jadwal.ID_KELAS', 'kelas.ID_KELAS')
            ->where('booking_kelas.ID_JADWAL', $CariJadwal['ID_JADWAL'])
            ->where('TANGGAL_KELAS', $today)
            ->get();

        $findMember = member::find($ID_MEMBER);
        if ($CariHargaKelas->count() > 0) {
            $firstCariHargaKelas = $CariHargaKelas->first();

            if ($firstCariHargaKelas['ID_JADWAL'] == $findMember['ID_JADWAL']) {
                $findMember['TEMP_PAKET'] -= 1;
            } else {
                $findMember['TEMP_UANG'] -= $firstCariHargaKelas['HARGA_KELAS'];
            }
            $findMember->update();
        }
    
        $findMember->update();
        
        $result = booking_kelas::where('ID_MEMBER', $ID_MEMBER)
            ->where('ID_JADWAL', $CariJadwal['ID_JADWAL'])
            ->where('TANGGAL_KELAS', $today)
            ->update(['STATUS_PRESENSI' => 2]);
            // dd($result);

        if ($CariHargaKelas) {
            return 'Pembaruan Dibuat Menjadi Hadir deposite Telah di Potong';
        } else {
            return 'Pembaruan  Hadir gagal';
        }
    }

    public function GetPresensiKelas($ID_JADWAL_HARIAN, $ID_INSTRUKTUR){

        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $CariJadwal = jadwal_harian::find($ID_JADWAL_HARIAN);

        $result = booking_kelas::join('jadwal', 'jadwal.ID_JADWAL', '=', 'booking_kelas.ID_JADWAL')
        ->join('kelas' , 'kelas.ID_KELAS' , '=' , 'jadwal.ID_KELAS')
        ->join('user', 'user.ID_USER' , '=' , 'booking_kelas.ID_USER')
        ->where('ID_INSTRUKTUR', $ID_INSTRUKTUR)
        ->whereColumn('jadwal.SESI_JADWAL', 'booking_kelas.SESI_BOOKING_KELAS')
        ->where('TANGGAL_KELAS', $today)
        ->orderBy('ID_MEMBER', 'asc')
        ->get();
            $jumlahData = $result->count();

            return response()->json([
                'status' => 'success',
                'message' => 'Data Jadwal Berhasil Ditampilkan',
                'data' => $result,
                'Jumlah data' => $jumlahData

            ], 200);

    }


}
