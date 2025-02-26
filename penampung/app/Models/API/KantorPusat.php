<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class KantorPusat extends Model
{
    protected $table = 'tb_kantor_pusat';

    protected $primaryKey = 'id_kantor_pusat';

    public $timestamps = false;

    protected $fillable = [
        'nama_kantor_pusat',
        'delete_kantor_pusat'
    ];
}
