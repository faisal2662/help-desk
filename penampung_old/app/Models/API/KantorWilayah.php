<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class KantorWilayah extends Model
{
    protected $table = 'tb_kantor_wilayah';

    protected $primaryKey = 'id_kantor_wilayah';

    public $timestamps = false;

    protected $fillable = [
        'nama_kantor_wilayah',
        'delete_kantor_wilayah'
    ];
}
