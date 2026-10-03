<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Transactions;


class WalletController extends Controller
{
    public function topUp(Request $request) 
    {
        // 1. Validasi Request JSON
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'FAILED',
                'message' => $validator->errors()->first()
            ], 400);
        }

        // 2. Ambil User yang sedang login lewat Token JWT
        $user = Auth::user();
        
        $amount = $request->amount;
        $balanceBefore = $user->balance;
        $balanceAfter = $balanceBefore + $amount;

        // 3. Update Saldo User
        $user->balance = $balanceAfter;
        $user->save();

        // 4. Simpan Riwayat Top Up (Menggunakan UUID sesuai contoh di gambar)
        $topUpId = (string) Str::uuid();

        // MENYIMPAN KE database 
        Transactions::create([
            'id' => $topUpId,
            'user_id' => $user->user_id, 
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'type' => 'TOPUP',
            'status' => 'SUCCESS',
            'description' => 'Top Up Saldo E-Wallet'
        ]);


        // 5. Kembalikan Response JSON SUCCESS sesuai gambar
        return response()->json([
            'status' => 'SUCCESS',
            'result' => [
                'top_up_id' => $topUpId,
                'amount_top_up' => (int) $amount,
                'balance_before' => (int) $balanceBefore,
                'balance_after' => (int) $balanceAfter,
                'created_date' => now()->format('Y-m-d H:i:s'),
            ]
        ], 200);
    }

     /**
     * FITUR: PAYMENT / PEMBAYARAN 
     */
    public function pay(Request $request)
    {
        // 1. Validasi Input JSON
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'remarks' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'FAILED',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $user = Auth::user();
        $amount = $request->amount;

        // 2. Validasi Apakah Saldo Cukup (Skenario FAILED pada Gambar)
        if ($user->balance < $amount) {
            return response()->json([
                'message' => 'Balance is not enough'
            ], 400);
        }

        $balanceBefore = $user->balance;
        $balanceAfter = $balanceBefore - $amount; // Saldo berkurang

        // Buat UUID acak untuk ID transaksi
        $transactionId = (string) Str::uuid();

        // 3. SIMPAN KE TABEL 'transactions' (Type: PAYMENT)
        Transactions::create([
            'id' => $transactionId,
            'user_id' => $user->user_id,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'type' => 'PAYMENT', 
            'status' => 'SUCCESS',
            'description' => $request->remarks 
        ]);

        // 4. Update saldo di tabel users
        $user->balance = $balanceAfter;
        $user->save();

        // 5. Kembalikan Response JSON SUCCESS Sesuai Gambar
        return response()->json([
            'status' => 'SUCCESS',
            'result' => [
                'payment_id' => $transactionId,
                'amount' => (int) $amount,
                'remarks' => $request->remarks,
                'balance_before' => (int) $balanceBefore,
                'balance_after' => (int) $balanceAfter,
                'created_date' => now()->format('Y-m-d H:i:s'),
            ]
        ], 200);
    }

    /**
     * FITUR: TRANSFER SALDO ANTAR PENGGUNA
     */
    public function transfer(Request $request)
    {
        // 1. Validasi Input JSON (target_user, amount, dan remarks wajib ada)
        //    PENTING: target_user sekarang berisi NOMOR HP penerima, bukan lagi UUID/user_id.
        $validator = Validator::make($request->all(), [
            'target_user' => 'required|string|max:20',
            'amount' => 'required|numeric|min:1',
            'remarks' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'FAILED',
                'message' => $validator->errors()->first()
            ], 400);
        }

        // 2. Ambil Data User Pengirim (yang sedang login)
        $sender = Auth::user();
        $amount = $request->amount;

        // 3. Validasi Apakah Saldo Pengirim Cukup (Skenario FAILED pada Gambar)
        if ($sender->balance < $amount) {
            return response()->json([
                'message' => 'Balance is not enough'
            ], 400);
        }

        // 4. Cari Data User Penerima berdasarkan NOMOR HP (kolom phone_number)
        $receiver = \App\Models\User::where('phone_number', $request->target_user)->first();

        // Jika nomor HP penerima tidak terdaftar
        if (!$receiver) {
            return response()->json([
                'status' => 'FAILED',
                'message' => 'Target user not found'
            ], 404); // 404 Not Found (sebelumnya tertulis 44 yang bukan kode HTTP valid)
        }

         // Validasi opsional agar tidak bisa transfer ke diri sendiri
        if ($sender->user_id === $receiver->user_id) {
            return response()->json([
                'status' => 'FAILED',
                'message' => 'Cannot transfer to yourself'
            ], 400);
        }

        $balanceBefore = $sender->balance;
        $balanceAfter = $balanceBefore - $amount;

        // 5. Kurangi Saldo Pengirim & Tambah Saldo Penerima
        $sender->balance = $balanceAfter;
        $sender->save();

        $receiver->balance = $receiver->balance + $amount;
        $receiver->save();

        // 6. Simpan Riwayat ke tabel 'transactions' (Type: TRANSFER)
        $transferId = (string) Str::uuid();
        Transactions::create([
            'id' => $transferId,
            'user_id' => $sender->user_id,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'type' => 'TRANSFER',
            'status' => 'SUCCESS',
            'description' => $request->remarks . " (To: " . $receiver->first_name . ")"
        ]);

        // 7. Kembalikan Response JSON SUCCESS Sesuai Gambar Spesifikasi
        return response()->json([
            'status' => 'SUCCESS',
            'result' => [
                'transfer_id' => $transferId,
                'amount' => (int) $amount,
                'remarks' => $request->remarks,
                'balance_before' => (int) $balanceBefore,
                'balance_after' => (int) $balanceAfter,
                'created_date' => now()->format('Y-m-d H:i:s'),
            ]
        ], 200);
    }

    /**
     * FITUR: REPORT TRANSACTIONS (Melihat Riwayat Transaksi Lengkap)
     */
    public function transactionReport()
    {
        // 1. Ambil user yang sedang login
        $user = Auth::user();

        // 2. Ambil semua transaksi milik user tersebut, diurutkan dari yang paling baru
        $transactions = Transactions::where('user_id', $user->user_id)
                                    ->orderBy('created_at', 'desc')
                                    ->get();

        // 3. Transformasi/Format ulang data agar persis sesuai spesifikasi gambar JSON
        $formattedResult = $transactions->map(function ($trx) {
            
            // Tentukan kategori DEBIT / CREDIT dan nama field ID dinamis
            if ($trx->type === 'TOPUP') {
                $idKey = 'top_up_id';
                $transactionType = 'CREDIT';
            } elseif ($trx->type === 'PAYMENT') {
                $idKey = 'payment_id';
                $transactionType = 'DEBIT';
            } else {
                $idKey = 'transfer_id';
                $transactionType = 'DEBIT';
            }

            return [
                $idKey => $trx->id, // Mengisi nama ID dinamis secara otomatis (misal: transfer_id)
                'status' => $trx->status,
                'user_id' => $trx->user_id,
                'transaction_type' => $transactionType,
                'amount' => (int) $trx->amount,
                'remarks' => $trx->description, // Menggunakan deskripsi tabel sebagai remarks
                'balance_before' => (int) $trx->balance_before,
                'balance_after' => (int) $trx->balance_after,
                'created_date' => $trx->created_at->format('Y-m-d H:i:s'),
            ];
        });

        // 4. Kembalikan Response JSON SUCCESS
        return response()->json([
            'status' => 'SUCCESS',
            'result' => $formattedResult
        ], 200);
    }

}
