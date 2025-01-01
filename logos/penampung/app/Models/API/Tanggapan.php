<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class Tanggapan extends Model
{
    protected $table = 'tb_tanggapan';

    protected $primaryKey = 'id_tanggapan';

    public $timestamps = false;

    protected $fillable = [
        'id_jawaban',
        'keterangan_tanggapan',
        'foto_tanggapan',
        'tgl_tanggapan',
        'delete_tanggapan'
    ];
}
