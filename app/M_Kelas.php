<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_Kelas extends Model
{
    protected $table = 'tbl_kelas';
    protected $primary_key = 'id_kelas';
    protected $fillable = [
        'id_kelas',
        'judul',
        'keterangan',
        'link_gambar',
        'pelajar',
        'materi'
    ];
}
