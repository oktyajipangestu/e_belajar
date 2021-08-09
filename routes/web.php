<?php

use App\Http\Controllers\Admin;

Route::post('/tambahAdmin', 'Admin@tambahAdmin');
Route::post('/loginAdmin', 'Admin@loginAdmin');
Route::post('/hapusAdmin', 'Admin@hapusAdmin');
Route::post('/listAdmin', 'Admin@listAdmin');

Route::post('/tambahPengajar', 'Pengajar@tambahPengajar');
Route::post('/loginPengajar', 'Pengajar@loginPengajar');
Route::post('/hapusPengajar', 'Pengajar@hapusPengajar');
Route::post('/listPengajar', 'Pengajar@listPengajar');

Route::post('/tambahPengajar', 'Pengajar@tambahPengajar');
Route::post('/loginPengajar', 'Pengajar@loginPengajar');
Route::post('/hapusPengajar', 'Pengajar@hapusPengajar');
Route::post('/listPelajar', 'Pelajar@listPelajar');

Route::post('/tambahMateri', 'Materi@tambahMateri');
Route::post('/editMateri', 'Materi@editMateri');
Route::post('/hapusMateri', 'Materi@hapusMateri');
Route::post('/listMateriPengajar', 'Materi@listMateriPengajar');
Route::post('/listMateriPelajar', 'Materi@listMateriPelajar');

Route::post('/tambahKelas', 'Kelas@tambahKelas');
Route::post('/editKelas', 'Kelas@editKelas');
Route::post('/hapusKelas', 'Kelas@hapusKelas');
Route::post('/listKelasPengajar', 'Kelas@listKelasPengajar');
Route::post('/listKelasPelajar', 'Kelas@listKelasPelajar');
Route::get('/listKelas', 'Kelas@listKelas');

Route::post('/registrasiPelajar', 'Pelajar@registrasiPelajar');
Route::post('/loginPelajar', 'Pelajar@loginPelajar');
Route::post('/listKelasPelajar', 'Kelas@listKelasPelajar');

Route::post('/listMateriPelajar', 'Materi@listMateriPelajar');
Route::post('/detailMateri', 'Materi@detailMateri');

Route::post('/listKomentar', 'Komentar@listKomentar');
Route::post('/tambahKomentar', 'Komentar@tambahKomentar');

Route::post('/onProgress', 'Progress@onProgress');
Route::post('/showProgress', 'Progress@showProgress');

Route::post('/quiz', 'Ujian@listSoal');
Route::post('/jawab', 'Ujian@jawab');
Route::post('/hitungSkor', 'Ujian@hitungSkor');
Route::post('/selesaiUjian', 'Ujian@selesaiUjian');
