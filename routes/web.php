<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

/*
|--------------------------------------------------------------------------
| Halaman Tampilan (View)
|--------------------------------------------------------------------------
| Halaman HTML yang memanggil API di routes/api.php melalui JavaScript (fetch).
|
| Catatan: endpoint POST /api/login sudah memakai nama route 'login', maka
| halaman form login di sini dinamai 'login.form' agar tidak saling menimpa.
*/
Route::view('/register', 'auth.register')->name('register');
Route::view('/login', 'auth.login')->name('login.form');

// Halaman transaksi; akses dijaga di sisi client menggunakan token JWT.
Route::view('/wallet', 'wallet.dashboard')->name('wallet');
