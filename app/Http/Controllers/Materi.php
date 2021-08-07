<?php

namespace App\Http\Controllers;

use App\M_Admin;
use App\M_Materi;
use App\M_Pelajar;
use App\M_Pengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use \Firebase\JWT\JWT;

class Materi extends Controller
{
    public function tambahMateri(Request $request){
        $validator = Validator::make($request->all(), [
            'judul' => 'required | unique:tbl_materi',
            'keterangan' => 'required',
            'link_thumbnail' => 'required',
            'link_video' => 'required'
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
                if(M_Materi::create([
                    'id_kelas' => $request->id_kelas,
                    'judul' => $request->judul,
                    'keterangan' => $request->keterangan,
                    'link_thumbnail' => $request->link_thumbnail,
                    'link_video' => $request->link_video
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

    public function editMateri(Request $request) {
        $validator = Validator::make($request->all(), [
            'judul' => 'required | unique:tbl_materi,judul,'.$request->id_materi.',id_materi',
            'keterangan' => 'required',
            'link_thumbnail' => 'required',
            'link_video' => 'required',
            'id_materi' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }

        $token = $request->token;
        $tokenDB = M_Materi::where('token', $token)->count();

        if($tokenDB > 0) {
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array) $decoded;

            if($decoded_array['extime'] > time()) {
                if(M_Materi::where('id_materi', $request->id_materi)->update([
                    'judul' => $request->judul,
                    'keterangan' => $request->keterangan,
                    'link_thumbnail' => $request->link_thumbnail,
                    'link_video' => $request->link_video
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


    public function hapusMateri(Request $request) {
        $validator = Validator::make($request ->all(), [
            'id_materi' => 'required',
            'token' => 'required'
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
            $decoded_array =(array) $decoded;

            if($decoded_array['extime'] > time()) {
                if(M_Materi::where('id_materi',$request->id_materi)->delete()) {
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

    public function listMateriPengajar(Request $request) {
        $validator = Validator::make($request ->all(), [
            'token' => 'required',
            'id_kelas' => 'required'
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
            $decoded_array =(array) $decoded;

            if($decoded_array['extime'] > time()) {
                $materi = M_Materi::where('id_kelas', $request->id)->get();

                return response()->json([
                    'status' => 'berhasil',
                    'message' => 'Data berhasil diambil',
                    'data' => $materi
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

    public function listMateriPelajar(Request $request) {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'id_kelas' => 'required'
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
                $materi = M_Materi::where('id_kelas' , $request->id_kelas)->get();

                return response()->json([
                    'status' => 'berhasil',
                    'message' => 'Data berhasil diambil',
                    'data' => $materi
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

    public function listMateriAdmin(Request $request) {
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
        $tokenDB = M_Admin::where('token', $token)->count();

        if($tokenDB > 0) {
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array =(array) $decoded;

            if($decoded_array['extime'] > time()) {
                $materi = M_Materi::get();

                return response()->json([
                    'status' => 'berhasil',
                    'message' => 'Data berhasil diambil',
                    'data' => $materi
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

    public function detailMateri(Request $request) {
        $validator = Validator::make($request->all(), [
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
        $tokenDB = M_Pelajar::where('token', $token)->count();

        if($tokenDB > 0) {
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array =(array) $decoded;

            if($decoded_array['extime'] > time()) {
                $materi = M_Materi::where('id_materi' , $request->id_materi)->get();

                return response()->json([
                    'status' => 'berhasil',
                    'message' => 'Data berhasil diambil',
                    'data' => $materi
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
