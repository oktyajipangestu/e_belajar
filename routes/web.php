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
