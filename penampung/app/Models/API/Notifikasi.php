<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'tb_notifikasi';

    protected $primaryKey = 'id_notifikasi';

    public $timestamps = false;

    protected $fillable = [
        'id_pegawai',
        'nama_notifikasi',
        'keterangan_notifikasi',
        'warna_notifikasi',
        'url_notifikasi',
        'status_notifikasi',
        'tgl_notifikasi',
        'delete_notifikasi'
    ];
}
