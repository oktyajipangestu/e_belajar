<?php

namespace App\Http\Controllers;

use App\M_Admin;
use App\M_Completed;
use App\M_Materi;
use App\M_Pelajar;
use App\M_Pengajar;
use App\M_Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use \Firebase\JWT\JWT;

class Progress extends Controller
{
    public function onProgress(Request $request) {
        $token = $request->token;
        $tokenDB = M_Pelajar::where('token', $token)->count();

        if($tokenDB > 0) {
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array =(array) $decoded;

            if($decoded_array['extime'] > time()) {
                $materi = M_Progress::where('id_pelajar', $decoded_array['id_pelajar'])->where('id_kelas', $request->id_kelas)->where('id_materi', $request->id_materi)->count();
                if($materi < 1) {
                    if(M_Progress::create([
                        'id_pelajar' => $decoded_array['id_pelajar'],
                        'id_kelas' => $request->id_kelas,
                        'id_materi' => $request->id_materi
                    ])){
                        $materi_completed = M_Progress::where('id_pelajar', $decoded_array['id_pelajar'])->where('id_kelas', $request->id_kelas)->count();
                        $jml_materi = M_Materi::where('id_kelas', $request->id_kelas)->count();
                        $completed = M_Completed::where('id_pelajar', $decoded_array['id_pelajar'])->count();

                        $skor_progress = $materi_completed / $jml_materi * 100;
                        if($completed > 0) {
                            if(M_Completed::where('id_pelajar', $decoded_array['id_pelajar'])->update([
                                'completed' => $skor_progress
                            ])) {
                                return response()->json([
                                    'status' => 'sukses',
                                    'message' => 'progress berhasil diperbaharui'
                                ]);
                            } else {
                                return response()->json([
                                    'status' => 'gagal',
                                    'message' => 'progress gagal diperbaharui'
                                ]);
                            }
                        } else {
                            if(M_Completed::create([
                                'id_pelajar' => $decoded_array['id_pelajar'],
                                'id_kelas' => $request->id_kelas,
                                'id_materi' => $request->id_materi,
                                'completed' => $skor_progress
                            ])) {
                                return response()->json([
                                    'status' => 'sukses',
                                    'message' => 'progress berhasil diperbaharui'
                                ]);
                            } else {
                                return response()->json([
                                    'status' => 'gagal',
                                    'message' => 'progress gagal diperbaharui'
                                ]);
                            }
                        }
                    }
                } else {
                    return response()->json([
                        'status' => 'gagal',
                        'message' => 'materi sudah dipelajari'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'gagal',
                    'message' => 'Token kadaluarsa'
                ]);
            }
        } else {
            return response()->json([
                'status' => 'gagal',
                'message' => 'Token Tidak Valid'
            ]);
        }
    }

    public function showProgress(Request $request) {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }

        $token = $request->token;

        $tokenDB = M_Admin::where('token', $token)->count();
        $tokenPelajar = M_Pelajar::where('token', $token)->count();
        $tokenPengajar = M_Pengajar::where('token', $token)->count();

        if($tokenDB > 0 || $tokenPelajar > 0 || $tokenPengajar > 0) {
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array =(array) $decoded;

            if($decoded_array['extime'] > time()) {
                $completed = M_Completed::where('id_pelajar', $decoded_array['id_pelajar'])->get();
                $completed_count = M_Completed::where('id_pelajar', $decoded_array['id_pelajar'])->count();
                $data = "";

                if($completed_count > 0) {
                    foreach($completed as $complete) {
                        $data =  $complete->completed;
                    }
                    return response()->json([
                        'status' => 'berhasil',
                        'message' => 'Data berhasil diambil',
                        'data' => $data
                    ]);
                } else {
                    return response()->json([
                        'status' => 'berhasil',
                        'message' => 'Data berhasil diambil',
                        'data' => "0"
                    ]);
                }


            } else {
                return response()->json([
                    'status' => 'gagal',
                    'message' => 'Token Kadaluwarsa'
                ]);
            }
        } else {
            return response()->json([
                'status' => 'gagal',
                'message' => 'Token Tidak Valid'
            ]);
        }
    }

}
