<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KepalaUnit extends Model
{
    use HasFactory;
    protected $table = 'tb_kepala_unit_kerja';
    protected $primaryKey = 'id_kepala_unit_kerja';

    protected $guarded = ['id_kepala_unit_kerja'];

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


}
