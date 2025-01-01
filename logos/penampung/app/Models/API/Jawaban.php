<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    protected $table = 'tb_jawaban';

    protected $primaryKey = 'id_jawaban';

    public $timestamps = false;

    protected $fillable = [
        'id_jawaban',
        'id_pegawai',
        'id_pengaduan',
        'keterangan_jawaban',
        'foto_jawaban',
        'sla_jawaban',
        'durasi_sla_jawaban',
        'alasan_sla_jawaban',
        'tgl_jawaban',
        'delete_jawaban'
    ];

    public function employee()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }

    public function response()
    {
        return $this->hasOne(Tanggapan::class, 'id_jawaban');
    }

    public function responses()
    {
        return $this->hasMany(Tanggapan::class, 'id_jawaban');
    }
}
