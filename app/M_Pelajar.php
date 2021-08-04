<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_Pelajar extends Model
{
    protected $table = 'tbl_pelajar';
    protected $primary_key = 'id_pelajar';
    protected $fillable = [
        'id_admin',
        'nama',
        'email',
        'password',
        'token',
        'materi_terakhir',
        'status'
    ];
}
