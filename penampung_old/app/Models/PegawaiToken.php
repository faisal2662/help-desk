<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PegawaiToken extends Model
{
    protected $table = 'tb_pegawai_token';

    protected $primaryKey = 'id_pegawai_token';

    public $timestamps = false;

    protected $fillable = [
        'id_pegawai_token',
        'id_pegawai',
        'token',
        'token_device',
        'ip_address',
        'tgl_terakhir_aktif',
        'tgl_pegawai_token',
        'delete_pegawai_token'
    ];

    public function employee()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }
}
