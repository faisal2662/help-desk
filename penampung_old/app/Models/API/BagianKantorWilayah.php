<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class BagianKantorWilayah extends Model
{
    protected $table = 'tb_bagian_kantor_wilayah';

    protected $primaryKey = 'id_bagian_kantor_wilayah';

    public $timestamps = false;

    protected $fillable = [
        'id_kantor_wilayah',
        'nama_bagian_kantor_wilayah',
        'delete_bagian_kantor_wilayah'
    ];

    public function regionalOffice()
    {
        return $this->belongsTo(KantorWilayah::class, 'id_kantor_wilayah');
    }
}
