<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KantorPusat extends Model
{
    protected $table = 'tb_kantor_pusat';

    protected $primaryKey = 'id_kantor_pusat';

    public $timestamps = false;

    protected $fillable = [
        'nama_kantor_pusat',
        'delete_kantor_pusat'
    ];


    /**
     * Get all of the BagianKantorPusat for the KantorPusat
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function BagianKantorPusat(): HasMany
    {
        return $this->hasMany(BagianKantorPusat::class, 'id_kantor_pusat', 'id_kantor_pusat');
    }

    
}
