<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_Pengajar extends Model
{
    protected $table = 'tbl_pengajar';
    protected $primary_key = 'id_pengajar';
    protected $fillable = [
        'id_admin',
        'nama',
        'email',
        'password',
        'token'
    ];
}
