<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/tambahAdmin',
        '/loginAdmin',
        '/hapusAdmin',
        '/listAdmin',
        '/tambahPengajar',
        '/loginPengajar',
        '/hapusPengajar',
        '/listPengajar',
        '/tambahMateri',
        '/editMateri',
        '/hapusMateri',
        '/listMateriPengajar',
        '/listMateriPelajar',
        '/tambahKelas',
        '/editKelas',
        '/hapusKelas',
        '/listKelasPengajar',
        '/listKelasPelajar',
        '/listPelajar',
        '/registrasiPelajar',
        '/loginPelajar',
        '/listMateriPelajar',
        '/detailMateri',
        '/listKomentar',
        '/tambahKomentar',
        '/onProgress',
        '/showProgress',
        '/quiz',
        '/jawab',
        '/hitungSkor',
        '/selesaiUjian',
        '/listKelas'
    ];
}
