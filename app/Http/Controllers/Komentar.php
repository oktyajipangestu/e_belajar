<?php

namespace App\Http\Controllers;

use App\M_Komentar;
use App\M_Pelajar;
use App\M_Pengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use \Firebase\JWT\JWT;

class Komentar extends Controller
{
    function tambahKomentar(Request $request) {
        $validator = Validator::make($request->all(), [
            'komentar' => 'required',
            'id_materi' => 'required',
            'nama_peserta' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }

        $token = $request->token;
        $tokenDB = M_Pelajar::where('token', $token)->count();

        if($tokenDB > 0) {
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array) $decoded;

            if($decoded_array['extime'] > time()){
                if(M_Komentar::create([
                    'id_materi' => $request->id_materi,
                    'nama_peserta' => $request->nama_peserta,
                    'komentar' => $request->komentar
                ])) {
                    return response()->json([
                        'status' => 'berhasil',
                        'message' => 'data berhasil ditambahkan'
                    ]);
                } else {
                    return response()->json([
                        'status' => 'gagal',
                        'message' => 'data gagal ditambahkan'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'gagal',
                    'message' => 'token kadaluwarsa'
                ]);
            }
        } else {
            return response()->json([
                'status' => 'gagal',
                'message' => 'token tidak valid'
            ]);
        }
    }

    function listKomentar(Request $request) {
        $validator = Validator::make($request ->all(), [
            'token' => 'required',
            'id_materi' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }

        $token = $request->token;
        $tokenPelajar = M_Pelajar::where('token', $token)->count();
        $tokenPengajar = M_Pengajar::where('token', $token)->count();

        if($tokenPelajar > 0 || $tokenPengajar > 0) {
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array =(array) $decoded;

            if($decoded_array['extime'] > time()) {
                $komentar = M_Komentar::where('id_materi', $request->id_materi)->get();

                return response()->json([
                    'status' => 'berhasil',
                    'message' => 'Data berhasil diambil',
                    'data' => $komentar
                ]);

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
