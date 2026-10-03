<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth; // Menggunakan package jwt-auth

class AuthController extends Controller
{
   // Fungsi utama untuk menangani proses register saat endpoint dipanggil
    public function register(Request $request)
    {
        // 1. Melakukan validasi data yang dikirim oleh client dari request JSON
        $validator = Validator::make($request->all(), [
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            // Memastikan nomor telepon wajib diisi dan harus unik (belum terdaftar di tabel users)
            'phone_number' => 'required|string|unique:users,phone_number',
            'address'      => 'required|string',
            // PIN wajib diisi, bertipe string, dan harus tepat 6 digit angka (0-9)
            'pin'          => 'required|string|digits:6',
        ]);

        // Jika validasi gagal (misal nomor telepon sudah ada), kembalikan pesan error sesuai spesifikasi
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Phone Number already registered'
            ], 400); // Kode HTTP 400 artinya Bad Request
        }

        try {
            // 2. Menyimpan data user baru ke database menggunakan Model User
            $user = User::create([
                'first_name'   => $request->first_name,
                'last_name'    => $request->last_name,
                'phone_number' => $request->phone_number,
                'address'      => $request->address,
                // Mengamankan PIN dengan cara di-hash (enkripsi satu arah) agar tidak terbaca mentah di database
                'pin'          => Hash::make($request->pin), 
            ]);

            // Mengubah format tanggal dari database agar sesuai dengan contoh output spesifikasi (YYYY-M-D H:i:s)
            $formattedDate = date('Y-n-j H:i:s', strtotime($user->created_date));

            // 3. Mengembalikan respons JSON dengan status sukses dan data user yang baru dibuat
            return response()->json([
                'status' => 'SUCCESS',
                'result' => [
                    'user_id'      => $user->user_id,
                    'first_name'   => $user->first_name,
                    'last_name'    => $user->last_name,
                    'phone_number' => $user->phone_number,
                    'address'      => $user->address,
                    'created_date' => $formattedDate,
                ]
            ], 201); // Kode HTTP 201 artinya Created (Berhasil dibuat)

        } catch (\Exception $e) {
            // Menangkap error tak terduga pada server
            return response()->json([
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    //login function
    public function login(Request $request)
    {
        // Validasi input awal jika diperlukan
        $phoneNumber = $request->input('phone_number');
        $pin = $request->input('pin');

        // Cari user berdasarkan nomor HP
        $user = User::where('phone_number', $phoneNumber)->first();

        // Cek apakah user ditemukan dan PIN cocok
        if (!$user || !Hash::check($pin, $user->pin)) {
            return response()->json([
                'message' => "Phone number and pin doesn't match."
            ], 401); // 401 Unauthorized atau 400 Bad Request sesuai kebutuhan kuliah Anda
        }

        // Generate JWT token (akses & refresh token)
        $accessToken = JWTAuth::fromUser($user);
        $refreshToken = JWTAuth::claims(['type' => 'refresh'])->fromUser($user); // Contoh dummy refresh token

        // Response SUCCESS sesuai format gambar
        return response()->json([
            'status' => 'SUCCESS',
            'result' => [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken
            ]
        ], 200);
    }

    /**
     * FITUR: PROFIL USER YANG SEDANG LOGIN
     *
     * Dipakai halaman view untuk menampilkan nama user dan saldo terkini
     * tanpa perlu mengetahui user_id (UUID) miliknya.
     */
    public function profile(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'status' => 'SUCCESS',
            'result' => [
                'user_id'      => $user->user_id,
                'first_name'   => $user->first_name,
                'last_name'    => $user->last_name,
                'phone_number' => $user->phone_number,
                'address'      => $user->address,
                'balance'      => (int) $user->balance,
            ]
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        // 1. Validasi Input JSON
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'address'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'FAILED',
                'message' => $validator->errors()->first()
            ], 400);
        }

        // 2. Ambil User Login
        $user = Auth::user();

        // 3. Update Data
        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->address    = $request->address;
        $user->save();

        // 4. Return Respons Sukses
        return response()->json([
            'status' => 'SUCCESS',
            'result' => [
                'user_id'      => $user->user_id,
                'first_name'   => $user->first_name,
                'last_name'    => $user->last_name,
                'address'      => $user->address,
                'updated_date' => now()->format('Y-m-d H:i:s'),
            ]
        ], 200);
    }
}
