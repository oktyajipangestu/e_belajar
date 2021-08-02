<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_Materi extends Model
{
    protected $table = 'tbl_materi';
    protected $primary_key = 'id_materi';
    protected $fillable = [
        'id_materi',
        'judul',
        'keterangan',
        'link_tumbnail',
        'link_video',
        'status',
        'view'
    ];
}
