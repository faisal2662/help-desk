<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alihkan extends Model
{
    protected $table = 'tb_alihkan';

    protected $primaryKey = 'id_alihkan';

    public $timestamps = false;

    protected $fillable = [
        'id_agent',
        'id_pengaduan',
        'id_pegawai',
        'keterangan_alihkan',
        'tgl_alihkan',
        'delete_alihkan'
    ];
}
