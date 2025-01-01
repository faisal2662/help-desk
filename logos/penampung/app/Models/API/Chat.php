<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'tb_chat';

    protected $primaryKey = 'id_chat';

    public $timestamps = false;

    protected $fillable = [
        'room_chat',
        'id_pegawai',
        'keterangan_chat',
        'foto_chat',
        'status_chat',
        'tgl_chat',
        'delete_chat'
    ];

    public function employee()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }
}
