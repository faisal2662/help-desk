<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pegawai extends Authenticatable
{
    protected $table = 'tb_pegawai';

    protected $primaryKey = 'id_pegawai';

    public $timestamps = false;

    protected $fillable = [
        'kantor_pegawai',
        'id_bagian_kantor_wilayah',
        'id_bagian_kantor_pusat',
        'id_bagian_kantor_cabang',
        'npp_pegawai',
        'nama_pegawai',
        'jenkel_pegawai',
        'telp_pegawai',
        'email_pegawai',
        'foto_pegawai',
        'password_pegawai',
        'level_pegawai',
        'status_pegawai',
        'sebagai_pegawai',
        'multi_pegawai',
        'api_token',
        'tgl_pegawai',
        'delete_pegawai'
    ];

    protected $hidden = [
        'password_pegawai'
    ];

    public function headOfficeSection()
    {
        return $this->belongsTo(BagianKantorPusat::class, 'id_bagian_kantor_pusat');
    }

    public function regionalOfficeSection()
    {
        return $this->belongsTo(BagianKantorWilayah::class, 'id_bagian_kantor_wilayah');
    }

    public function branchOfficeSection()
    {
        return $this->belongsTo(BagianKantorCabang::class, 'id_bagian_kantor_cabang');
    }

    public function headOffice()
    {
        return $this->belongsTo(KantorPusat::class, 'multi_pegawai', 'id_kantor_pusat');
    }

    public function regionalOffice()
    {
        return $this->belongsTo(KantorWilayah::class, 'multi_pegawai', 'id_kantor_wilayah');
    }

    public function branchOffice()
    {
        return $this->belongsTo(KantorCabang::class, 'multi_pegawai', 'id_kantor_cabang');
    }

    public function notifications()
    {
        return $this->hasMany(Notifikasi::class, 'id_pegawai');
    }
}
