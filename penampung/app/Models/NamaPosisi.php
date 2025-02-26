<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NamaPosisi extends Model
{
    use HasFactory;

    protected $table = 'tb_posisi_pegawai';

    protected $guarded = ['id_posisi_pegawai'];
    protected $primaryKey = 'id_posisi_pegawai';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';


    /**
     * Get the Pegawai associated with the Checked
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function Pegawai(): HasMany
    {
        return $this->hasMany(Pegawai::class, 'id_posisi_pegawai', 'id_posisi_pegawai');
    }

    // /**
    //  * Get the Pengaduan associated with the Checked
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\HasOne
    //  */
    // public function Pengaduan(): HasOne
    // {
    //     return $this->hasOne(Pengaduan::class, 'id_pengaduan', 'id_pengaduan');
    // }
}
