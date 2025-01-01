<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dibaca extends Model
{
    protected $table = 'tb_dibaca';

    protected $primaryKey = 'id_dibaca';

    public $timestamps = false;

    protected $fillable = [
        'id_pengaduan',
        'id_pegawai',
        'tgl_dibaca',
        'delete_dibaca'
    ];

    public function employee()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }
}
