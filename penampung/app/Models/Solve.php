<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Solve extends Model
{
    use HasFactory;

    protected $table = 'tb_solve';

    protected $guarded = ['id_solve'];
    protected $primaryKey = 'id_solve';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';


    /**
     * Get the Pegawai associated with the Checked
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function Pegawai(): HasOne
    {
        return $this->hasOne(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    /**
     * Get the Pengaduan associated with the Checked
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function Pengaduan(): HasOne
    {
        return $this->hasOne(Pengaduan::class, 'id_pengaduan', 'id_pengaduan');
    }
}
