<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PegawaiSunfish extends Model
{
    use HasFactory;

    use HasFactory;


    protected $table = 'tb_pegawai_sunfish';

    protected $guarded = ['id_pegawai_sunfish'];
    protected $primaryKey = 'id_pegawai_sunfish';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';



}

