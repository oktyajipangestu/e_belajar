<?php

namespace App\Http\Controllers;

use App\M_Pengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use \Firebase\JWT\JWT;

class Pengajar extends Controller
{
    public function tambahPengajar(Request $request) {
        $validator = Validator::make($request, [
            'nama' => 'required',
            'email' => 'required | unique:tbl_user',
            'password' => 'required',
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
            $decoded_array = (array) $decoded;

            if($decoded_array['extime'] > time()) {
                if(M_Pengajar::create([
                    'nama' => $request->nama,
                    'email' => $request->email,
                    'password' => encrypt($request->password)
                ])) {
                    return response()->json([
                        'status' => 'berhasil',
                        'message' => 'data berhasil disimpan'
                    ]);
                } else {
                    return response()->json([
                        'status' => 'gagal',
                        'message' => 'data gagal disimpan'
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

    public function loginPengajar(Request $request) {
        $validator = Validator::make($request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }

        $cek = M_Pengajar::where('email', $request->email)->count();
        $admin = M_Pengajar::whare('email', $request->email)->get();

        if($cek > 1) {
            foreach($admin as $adm) {
                if($request->password === decrypt($adm->password)) {
                    $key = env('APP_KEY');
                    $data = array(
                        'extime'=>time()+(60*120),
                        'id_pengajar' => $adm->id_pengajar
                    );

                    $jwt = JWT::encode($data, $key);

                    M_Pengajar::where('id_pengajar', $adm->id_pengajar)->update(
                        [
                            'token' => $jwt
                        ]
                    );

                    return response()->json([
                        'status' => 'berhasil',
                        'message' => 'berhasil login',
                        'token' => $jwt
                    ]);
                } else {
                    return response()->json([
                        'status' => 'gagal',
                        'message' => 'password salah'
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 'gagal',
                'message' => 'email belum terdaftar'
            ]);
        }

    }

    public function hapusPengajar(Request $request) {
        $validator = Validator::make($request, [
            'id_pengajar' => 'required',
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
            $decoded_array = (array) $decoded;

            if($decoded_array['extime'] > time()) {
                if(M_Pengajar::where('id_pengajar', $request->id_pengajar)->delete()){
                    return response()->json([
                        'status' => 'berhasil',
                        'message' => 'data berhasil dihapus'
                    ]);
                } else {
                    return response()->json([
                        'status' => 'gagal',
                        'message' => 'data gagal dihapus'
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 'gagal',
                'message' => 'token kadaluwarsa'
            ]);
        }

    }

    public function listAdmin(Request $request) {
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
                $pengajar = M_Pengajar::get();

                $data = array();

                foreach($pengajar as $guru) {
                    $data[] = array(
                        'nama' => $guru->nama,
                        'email' => $guru->email,
                        'id_admin' => $guru->id_admin
                    );
                }

                return response()->json([
                    'status' => 'berhasil',
                    'message' => 'Data berhasil diambil',
                    'data' => $data
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
