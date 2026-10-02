<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Fungsi 'up' dijalankan saat perintah 'php artisan migrate' dieksekusi untuk membuat tabel baru di database
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Membuat kolom 'user_id' dengan tipe data UUID dan menjadikannya sebagai primary key (kunci utama)
            $table->uuid('user_id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number')->unique();
            $table->text('address')->nullable();
            $table->string('pin');
            $table->bigInteger('balance')->default(0);
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    // Fungsi 'down' dijalankan jika kita ingin membatalkan/menghapus migrasi (rollback)
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};