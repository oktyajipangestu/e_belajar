<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_Materi extends Model
{
    protected $table = 'tbl_materi';
    protected $primary_key = 'id_materi';
    protected $fillable = [
        'id_materi',
        'id_kelas',
        'judul',
        'keterangan',
        'link_thumbnail',
        'link_video',
        'status',
        'view'
    ];
}
