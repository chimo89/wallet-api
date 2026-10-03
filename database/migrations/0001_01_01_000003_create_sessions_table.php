<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel `sessions` dibutuhkan karena SESSION_DRIVER pada .env bernilai "database".
     *
     * Pada skeleton Laravel, tabel ini dibuat di dalam migrasi create_users_table.
     * Migrasi tersebut di proyek ini diganti dengan skema kustom (hanya tabel users),
     * sehingga tabel sessions tidak pernah terbuat dan halaman web (yang melewati
     * middleware session) menjadi error 500.
     *
     * Catatan: kolom user_id memakai tipe uuid agar konsisten dengan primary key
     * tabel users (user_id berbentuk UUID string).
     */
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->uuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};