<?php

namespace App\Models;

;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SetupWhatsapp extends Model
{
     use HasFactory;
    protected $table = 'tb_setup_whatsapp';

    protected $primaryKey = 'id_setup_whatsapp';

    // public $timestamps = false;

    protected $guarded = ['id_setup_whatsapp'];
     const CREATED_AT = 'created_date';
     const UPDATED_AT = 'updated_date';



}
