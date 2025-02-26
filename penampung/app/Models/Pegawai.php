<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pegawai extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'tb_pegawai';
    protected $guarded = ['id_pegawai'];
    protected $primaryKey = 'id_pegawai';
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the NamaPosisi associated with the Pegawai
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function NamaPosisi(): HasOne
    {
        return $this->hasOne(NamaPosisi::class, 'id_posisi_pegawai', 'id_posisi_pegawai');
    }
}
