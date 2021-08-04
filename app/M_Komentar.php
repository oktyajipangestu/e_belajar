<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_Komentar extends Model
{
    protected $table = 'tbl_komentar';
    protected $primaryKey = 'id_komentar';
    protected $fillable = [
        'id_komentar',
        'id_materi',
        'nama_peserta',
        'komentar'
    ];
}
