<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KantorWilayah extends Model
{
    protected $table = 'tb_kantor_wilayah';

    protected $primaryKey = 'id_kantor_wilayah';

    public $timestamps = false;

    protected $fillable = [
        'nama_kantor_wilayah',
        'delete_kantor_wilayah'
    ];
     /**
     * Get all of the BagianKantorWilayah for the KantorWilayah
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function BagianKantorWilayah(): HasMany
    {
        return $this->hasMany(BagianKantorWilayah::class, 'id_kantor_wilayah', 'id_kantor_wilayah');
    }
}
