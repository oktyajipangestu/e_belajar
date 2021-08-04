<?php

namespace App\Http\Controllers;

use App\M_Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use \Firebase\JWT\JWT;

use Illuminate\Http\Response;

use Illuminate\Support\Facades\Validator;

use Illuminate\Contracts\Encryption\DecryptException;

use App\M_Pelajar;
use App\M_Pengajar;

class Pelajar extends Controller
{
    public function registrasiPelajar(Request $request) {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required | unique:tbl_pelajar',
            'password' => 'required | confirmed',
            'password_confirmation' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }

        if(M_Pelajar::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => encrypt($request->password)
        ])) {
            return response()->json([
                'status' => 'berhasil',
                'message' => 'Data berhasil disimpan'
            ]);
        } else {
            return response()->json([
                'status' => 'gagal',
                'message' => 'Data gagal disimpan'
            ]);
        }

    }

    public function loginPelajar(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }

        $cek = M_Pelajar::where('email', $request->email)->count();
        $pelajar = M_Pelajar::where('eamil', $request->email)->get();

        if($cek > 0) {
            foreach($pelajar as $siswa) {
                if($request->password === decrypt($siswa->password)) {
                    $key = env('APP_KEY');
                    $data = array(
                        'extime' => time() + (60*120),
                        'id_pelajar' => $siswa->id_pelajar
                    );

                    $jwt = JWT::encode($data, $key);

                    if(M_Pelajar::where('id_pelajar', $siswa->id_pelajar)->update(
                        [
                            'token' => $jwt
                        ]
                    )) {
                        return response()->json([
                            'status' => 'berhasil',
                            'message' => 'Berhasil login',
                            'token' => $jwt
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'berhasil',
                            'message' => 'Gagal Login'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 'gagal',
                        'message' => 'Password Salah'
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 'gagal',
                'message' => 'Email belum terdaftar'
            ]);
        }
    }

    public function listPelajar(Request $request) {
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
        $tokenAdmin = M_Admin::where('token', $token)->count();
        $tokenPengajar = M_Pengajar::where('token', $token)->count();

        if($tokenAdmin > 0 || $tokenPengajar > 0) {
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array =(array) $decoded;

            if($decoded_array['extime'] > time()) {
                $pelajar = M_Pelajar::get();

                $data = array();

                foreach($pelajar as $siswa) {
                    $data[] = array(
                        'nama' => $siswa->nama,
                        'email' => $siswa->email,
                        'id_pelajar' => $siswa->id_pelajar
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
