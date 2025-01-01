<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BagianKantorPusat extends Model
{
    protected $table = 'tb_bagian_kantor_pusat';

    protected $primaryKey = 'id_bagian_kantor_pusat';

    public $timestamps = false;

    protected $fillable = [
        'id_kantor_pusat',
        'nama_bagian_kantor_pusat',
        'delete_bagian_kantor_pusat'
    ];

    public function headOffice()
    {
        return $this->belongsTo(KantorPusat::class, 'id_kantor_pusat');
    }
}
