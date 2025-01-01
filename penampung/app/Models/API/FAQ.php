<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    protected $table = 'tb_faq';

    protected $primaryKey = 'id_faq';

    public $timestamps = false;

    protected $fillable = [
        'pertanyaan_faq',
        'keterangan_faq',
        'urutan_faq',
        'tgl_faq',
        'delete_faq'
    ];
}
