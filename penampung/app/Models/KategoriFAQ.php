<?php

namespace App\Models;

use App\Http\Controllers\FAQ;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriFAQ extends Model
{
    use HasFactory;


    protected $table = 'tb_kategori_faq';

    protected $guarded = ['id_kategori_faq'];
    protected $primaryKey = 'id_kategori_faq';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';


   /**
    * Get all of the FAQ for the KategoriFAQ
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
   public function FAQ(): HasMany
   {
       return $this->hasMany(FAQ::class, 'id_kategori_faq', 'id_kategori_faq');
   }
}
