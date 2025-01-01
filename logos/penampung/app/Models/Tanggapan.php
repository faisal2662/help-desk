<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tanggapan extends Model
{
    protected $table = 'tb_tanggapan';

    protected $primaryKey = 'id_tanggapan';

    public $timestamps = false;

    protected $fillable = [
        'id_jawaban',
        'id_pegawai',
        'keterangan_tanggapan',
        'foto_tanggapan',
        'tgl_tanggapan',
        'delete_tanggapan'
    ];
    
      public function employee()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }
}
