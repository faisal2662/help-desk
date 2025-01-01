<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class Lampiran extends Model
{
    protected $table = 'tb_lampiran';

    protected $primaryKey = 'id_lampiran';

    public $timestamps = false;

    protected $fillable = [
        'id_pengaduan',
        'file_lampiran',
        'delete_lampiran'
    ];
}
