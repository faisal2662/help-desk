<?php

namespace App\Models;

use App\Models\Jawaban;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserFAQ extends Model
{
     use HasFactory;
    protected $table = 'tb_user_faq';

    protected $primaryKey = 'id_faq';

    // public $timestamps = false;

    protected $guarded = ['id_faq'];
     const CREATED_AT = 'tgl_faq';
     const UPDATED_AT = 'updated_date';
    /**
     * Get the jawaban associated with the FAQ
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function jawaban(): HasOne
    {
        return $this->hasOne(Jawaban::class, 'id_fad', 'id_faq');
    }

    /**
     * Get the KategoriFAQ associated with the FAQ
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function KategoriFAQ(): HasOne
    {
        return $this->hasOne(KategoriFAQ::class, 'id_kategori_faq', 'id_kategori_faq');
    }
}
