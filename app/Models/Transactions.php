<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $table = 'transactions';

    // 2. Karena kita pakai UUID string (bukan angka 1, 2, 3 otomatis), matikan aturan bawaan Laravel
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    // 3. Daftar kolom yang BOLEH diisi otomatis oleh Controller
    protected $fillable = [
        'id',
        'user_id',
        'amount',
        'balance_before',
        'balance_after',
        'type',
        'status',
        'description',
    ];
}
