<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class Selesai extends Model
{
    protected $table = 'tb_selesai';

    protected $primaryKey = 'id_selesai';

    public $timestamps = false;

    protected $fillable = [
        'id_pengaduan',
        'id_pegawai',
        'tgl_selesai',
        'delete_selesai'
    ];

    public function employee()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }
}
