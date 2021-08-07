<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_Progress extends Model
{
    protected $table = 'tbl_progress';
    protected $primary_key = 'id_progress';
    protected $fillable = [
        'id_progress',
        'id_pelajar',
        'id_kelas',
        'id_materi'
    ];
}
