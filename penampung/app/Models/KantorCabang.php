<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KantorCabang extends Model
{
    protected $table = 'tb_kantor_cabang';

    protected $primaryKey = 'id_kantor_cabang';

    public $timestamps = false;

    protected $fillable = [
        'nama_kantor_cabang',
        'delete_kantor_cabang'
    ];
    
     /**
     * Get all of the BagianKantorCabagn for the KantorCabang
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function BagianKantorCabang(): HasMany
    {
        return $this->hasMany(BagianKantorCabang::class, 'id_kantor_cabang', 'id_kantor_cabang');
    }
}
