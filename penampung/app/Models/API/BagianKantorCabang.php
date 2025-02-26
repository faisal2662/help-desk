<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class BagianKantorCabang extends Model
{
    protected $table = 'tb_bagian_kantor_cabang';

    protected $primaryKey = 'id_bagian_kantor_cabang';

    public $timestamps = false;

    protected $fillable = [
        'id_kantor_cabang',
        'nama_bagian_kantor_cabang',
        'delete_bagian_kantor_cabang'
    ];

    public function branchOffice()
    {
        return $this->belongsTo(KantorCabang::class, 'id_kantor_cabang');
    }
}
