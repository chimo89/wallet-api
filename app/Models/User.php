<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject; 

class User extends Authenticatable implements JWTSubject
{
    // Menggunakan trait bawaan Laravel untuk fitur token API dan factory data
    use HasApiTokens, HasFactory;

    // Menentukan nama tabel di database yang terhubung dengan model ini
    protected $table = 'users';

    // Memberitahu Laravel bahwa primary key tabel ini bernama 'user_id' (bukan 'id' standar)
    protected $primaryKey = 'user_id';

    // Menonaktifkan auto-increment karena kita menggunakan UUID (bukan angka 1, 2, 3...)
    public $incrementing = false;

    // Menentukan tipe data primary key adalah string (karena format UUID berupa teks)
    protected $keyType = 'string';
    
    // Mengubah nama konstanta bawaan Laravel agar mencatat waktu ke kolom 'created_date'
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_at';

    // Daftar kolom yang diizinkan untuk diisi secara massal (mass assignment)
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone_number',
        'address',
        'pin',
    ];

    // Daftar kolom yang disembunyikan agar tidak ikut muncul saat data user dikirim ke respons JSON (demi keamanan)
    protected $hidden = [
        'pin',
    ];

    // Fungsi 'boot' dijalankan otomatis oleh Laravel saat ada event data mau dibuat (creating)
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            // Jika 'user_id' belum diisi, otomatis buatkan string UUID acak yang unik
            if (empty($model->user_id)) {
                $model->user_id = (string) Str::uuid();
            }
        });
    }


     public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}