<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class KantorCabang extends Model
{
    protected $table = 'tb_kantor_cabang';

    protected $primaryKey = 'id_kantor_cabang';

    public $timestamps = false;

    protected $fillable = [
        'nama_kantor_cabang',
        'delete_kantor_cabang'
    ];
}
