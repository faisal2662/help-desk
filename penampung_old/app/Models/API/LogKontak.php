<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class LogKontak extends Model
{
    protected $table = 'tb_log_kontak';

    protected $primaryKey = 'id_log_kontak';

    public $timestamps = false;

    protected $fillable = [
        'id_kontak',
        'id_pegawai',
        'role_log_kontak',
        'keterangan_log_kontak',
        'status_log_kontak',
        'ajax_log_kontak',
        'song_log_kontak',
        'tgl_log_kontak',
        'delete_log_kontak'
    ];
}
