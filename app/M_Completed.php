<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_Completed extends Model
{
    protected $table = 'tbl_completed';
    protected $primary_key = 'id_completed';
    protected $fillable = [
        'id_completed',
        'id_pelajar',
        'id_kelas',
        'completed'
    ];
}
