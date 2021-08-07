<?php

namespace App\Http\Controllers;

use App\M_Kelas;
use App\M_Materi;
use App\M_Pelajar;
use App\M_Pengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use \Firebase\JWT\JWT;

class Kelas extends Controller
{
    public function tambahKelas(Request $request) {
        $validator = Validator::make($request->all(), [
            'judul' => 'required | unique:tbl_materi',
            'keterangan' => 'required',
            'link_gambar' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }

        $token = $request->token;
        $tokenDB = M_Pengajar::where('token', $token)->count();

        if($tokenDB > 0) {
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array) $decoded;

            if($decoded_array['extime'] > time()){
                if(M_Kelas::create([
                    'judul' => $request->judul,
                    'keterangan' => $request->keterangan,
                    'link_gambar' => $request->link_gambar
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

    public function editKelas(Request $request) {
        $validator = Validator::make($request ->all(), [
            'judul' => 'required | unique:tbl_kelas,judul,'.$request->id_kelas.',id_kelas',
            'keterangan' => 'required',
            'link_gambar' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }

        $token = $request->token;
        $tokenDB = M_Pengajar::where('token', $token)->count();

        if($tokenDB > 0) {
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array) $decoded;

            if($decoded_array['extime'] > time()) {
                if(M_Kelas::where('id_kelas', $request->id_kelas)->update([
                    'judul' => $request->judul,
                    'keterangan' => $request->keterangan,
                    'link_gambar' => $request->link_gambar
                ])) {
                    return response()->json([
                        'status' => 'berhasil',
                        'message' => 'data berhasil diubah'
                    ]);
                } else {
                    return response()->json([
                        'status' => 'gagal',
                        'message' => 'data gagal diubah'
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

    public function hapusKelas(Request $request) {
        $validator = Validator::make($request ->all(), [
            'id_kelas' => 'required',
            'token' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }

        $token = $request->token;
        $tokenDb = M_Pengajar::where('token', $token)->count();

        if($tokenDb > 0) {
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array =(array) $decoded;

            if($decoded_array['extime'] > time()) {
                if(M_Kelas::where('id_kelas',$request->id_kelas)->delete()) {
                        return response()->json([
                            'status' => 'berhasil',
                            'message' => 'Data berhasil dihapus'
                        ]);
                } else {
                    return response()->json([
                        'status' => 'gagal',
                        'message' => 'Data gagal dihapus'
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

    public function listKelasPengajar(Request $request) {
        $validator = Validator::make($request ->all(), [
            'token' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }

        $token = $request->token;
        $tokenDb = M_Pengajar::where('token', $token)->count();

        if($tokenDb > 0) {
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array =(array) $decoded;

            if($decoded_array['extime'] > time()) {
                $kelas = M_Kelas::get();

                return response()->json([
                    'status' => 'berhasil',
                    'message' => 'Data berhasil diambil',
                    'data' => $kelas
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

    public function listKelasPelajar(Request $request) {
        $validator = Validator::make($request ->all(), [
            'token' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }

        $token = $request->token;
        $tokenDb = M_Pelajar::where('token', $token)->count();

        if($tokenDb > 0) {
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array =(array) $decoded;

            if($decoded_array['extime'] > time()) {
                $kelas = M_Kelas::get();

                return response()->json([
                    'status' => 'berhasil',
                    'message' => 'Data berhasil diambil',
                    'data' => $kelas
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


    public function detailKelas(Request $request, $id) {
        $validator = Validator::make($request ->all(), [
            'token' => 'required'
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
            $decoded_array =(array) $decoded;

            if($decoded_array['extime'] > time()) {
                $kelas = M_Kelas::get();

                return response()->json([
                    'status' => 'berhasil',
                    'message' => 'Data berhasil diambil',
                    'data' => $kelas
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
