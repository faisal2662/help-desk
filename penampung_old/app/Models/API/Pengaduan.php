<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $table = 'tb_pengaduan';

    protected $primaryKey = 'id_pengaduan';

    public $timestamps = false;

    protected $fillable = [
        'id_pegawai',
        'kantor_pengaduan',
        'id_bagian_kantor_pusat',
        'id_bagian_kantor_wilayah',
        'id_bagian_kantor_cabang',
        'nama_pengaduan',
        'keterangan_pengaduan',
        'klasifikasi_pengaduan',
        'status_pengaduan',
        'respon_pengaduan',
        'tgl_pengaduan',
        'delete_pengaduan'
    ];

    protected $casts = [
        'id_pengaduan' => 'integer',
        'id_bagian_kantor_pusat' => 'integer',
        'id_bagian_kantor_wilayah' => 'integer',
        'id_bagian_kantor_cabang' => 'integer',
        'tgl_pengaduan' => 'datetime'
    ];

    public function employee()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }

    public function headOfficeSection()
    {
        return $this->belongsTo(BagianKantorPusat::class, 'id_bagian_kantor_pusat');
    }

    public function regionalOfficeSection()
    {
        return $this->belongsTo(BagianKantorWilayah::class, 'id_bagian_kantor_wilayah');
    }

    public function branchOfficeSection()
    {
        return $this->belongsTo(BagianKantorCabang::class, 'id_bagian_kantor_cabang');
    }

    public function answers()
    {
        return $this->hasMany(Jawaban::class, 'id_pengaduan');
    }

    public function attachments()
    {
        return $this->hasMany(Lampiran::class, 'id_pengaduan');
    }

    public function knows()
    {
        return $this->hasMany(Mengetahui::class, 'id_pengaduan');
    }

    public function done()
    {
        return $this->hasOne(Selesai::class, 'id_pengaduan');
    }

    public function reads()
    {
        return $this->hasMany(Dibaca::class, 'id_pengaduan');
    }
}
