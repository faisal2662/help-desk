<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Pengaduan extends Model
{
    protected $table = 'tb_pengaduan';

    protected $primaryKey = 'id_pengaduan';

    // public $timestamps = false;

    protected $guarded = ['id_pengaduan'];

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'update_date';




/**
 * Get the Pegawai associated with the Pengaduan
 *
 * @return \Illuminate\Database\Eloquent\Relations\HasOne
 */
public function Pegawai(): HasOne
{
    return $this->hasOne(Pegawai::class, 'id_pegawai', 'id_pegawai');
}

   /**
     * Get the BagianKantorPusa associated with the Pegawai
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function BagianKantorPusat(): HasOne
    {
        return $this->hasOne(BagianKantorPusat::class, 'id_bagian_kantor_pusat', 'id_bagian_kantor_pusat');
    }
    /**
     * Get the BagianKantorPusa associated with the Pegawai
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function BagianKantorCabang(): HasOne
    {
        return $this->hasOne(BagianKantorCabang::class, 'id_bagian_kantor_cabang', 'id_bagian_kantor_cabang');
    }
    /**
     * Get the BagianKantorPusa associated with the Pegawai
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function BagianKantorWilayah(): HasOne
    {
        return $this->hasOne(BagianKantorWilayah::class, 'id_bagian_kantor_wilayah', 'id_bagian_kantor_wilayah');
    }
    // public function employee()
    // {
    //     return $this->belongsTo(Pegawai::class, 'id_pegawai');
    // }

    // public function headOfficeSection()
    // {
    //     return $this->belongsTo(BagianKantorPusat::class, 'id_bagian_kantor_pusat');
    // }

    // public function regionalOfficeSection()
    // {
    //     return $this->belongsTo(BagianKantorWilayah::class, 'id_bagian_kantor_wilayah');
    // }

    // public function branchOfficeSection()
    // {
    //     return $this->belongsTo(BagianKantorCabang::class, 'id_bagian_kantor_cabang');
    // }

    // public function answers()
    // {
    //     return $this->hasMany(Jawaban::class, 'id_pengaduan');
    // }

    // public function attachments()
    // {
    //     return $this->hasMany(Lampiran::class, 'id_pengaduan');
    // }

    // public function knows()
    // {
    //     return $this->hasMany(Mengetahui::class, 'id_pengaduan');
    // }

    // public function done()
    // {
    //     return $this->hasOne(Selesai::class, 'id_pengaduan');
    // }

    // public function reads()
    // {
    //     return $this->hasMany(Dibaca::class, 'id_pengaduan');
    // }
}
