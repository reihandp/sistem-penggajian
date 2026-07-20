<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model untuk tabel users
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class User extends Authenticatable
{
    // ======================================================================
    // 1. TRAITS
    // ======================================================================

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // ======================================================================
    // 2. KONFIGURASI MASS ASSIGNMENT
    // ======================================================================

    /**
     * Atribut-atribut yang diizinkan untuk diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // ======================================================================
    // 3. KONFIGURASI HIDDEN ATTRIBUTES
    // ======================================================================

    /**
     * Atribut-atribut yang harus disembunyikan saat serialisasi.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ======================================================================
    // 4. CASTING ATRIBUT
    // ======================================================================

    /**
     * Mendefinisikan casting tipe data untuk atribut-atribut tertentu.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}