<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_Skor extends Model
{
    protected $table = 'tbl_skor';
    protected $primaryKey = 'id_skor';
    protected $fillable = [
        'id_skor',
        'id_kelas',
        'id_pelajar',
        'skor',
        'status'
    ];
}
