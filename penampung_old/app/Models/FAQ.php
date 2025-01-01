<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    protected $table = 'tb_faq';

    protected $primaryKey = 'id_faq';

   const CREATED_AT = 'tgl_faq';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
        'pertanyaan_faq',
        'jawaban_faq',
        'urutan_faq',
        'tgl_faq',
        'delete_faq'
    ];
}
