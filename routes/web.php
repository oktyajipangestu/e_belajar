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
