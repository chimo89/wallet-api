<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
           // 1. Primary Key untuk transaksi top up ini sendiri (UUID)
            $table->uuid('id')->primary();
            
            // 2. Foreign Key yang menyambung ke kolom user_id di tabel users
            $table->uuid('user_id');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            
            // 3. Kolom detail transaksi sesuai dengan spesifikasi API Anda sebelumnya
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('type'); 
            $table->enum('status', ['PENDING', 'SUCCESS', 'FAILED'])->default('SUCCESS');
            $table->text('description')->nullable();
            
            // 4. Timestamps (Otomatis membuat kolom created_at dan updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
