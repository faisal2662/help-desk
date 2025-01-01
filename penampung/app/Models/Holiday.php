<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{

    use HasFactory;


    protected $table = 'tb_hari_libur';

    protected $guarded = ['id_hari_libur'];
    protected $primaryKey = 'id_hari_libur';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';


}
