<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mengetahui extends Model
{
    protected $table = 'tb_mengetahui';

    protected $primaryKey = 'id_mengetahui';

    public $timestamps = false;

    protected $fillable = [
        'id_pengaduan',
        'id_pegawai',
        'tgl_mengetahui',
        'delete_mengetahui'
    ];

    public function complaint()
    {
        return $this->belongsTo(Pengaduan::class, 'id_pengaduan');
    }

    public function employee()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }
}
