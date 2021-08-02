<?php

namespace App\Http\Controllers;

use App\M_Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use \Firebase\JWT\JWT;

class Admin extends Controller
{
    public function tambahAdmin(Request $request) {
        $validator = Validator::make($request ->all(), [
            'nama' => 'required',
            'email' => 'required | unique:tbl_admin',
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
        $tokenDB = M_Admin::where('token', $token)->count();

        if($tokenDB > 0) {
            $key = env('APP_KEY');

            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array) $decoded;

            if($decoded_array['extime'] > time()) {
                if(M_Admin::create([
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

    public function loginAdmin(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }

        $cek = M_Admin::where('email', $request->email)->count();
        $admin = M_Admin::where('email', $request->email)->get();

        if($cek > 0) {
            foreach($admin as $adm) {
                if($request->password === decrypt($adm->password)) {
                    $key = env('APP_KEY');
                    $data = array(
                        'extime'=>time()+(60*120),
                        'id_admin' => $adm->id_admin
                    );

                    $jwt = JWT::encode($data, $key);

                    M_Admin::where('id_admin', $adm->id_admin)->update(
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

    public function hapusAdmin(Request $request) {
        $validator = Validator::make($request->all(), [
            'id_admin' => 'required',
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
            $decoded_array = (array) $decoded;

            if($decoded_array['extime'] > time()) {
                if(M_Admin::where('id_admin', $request->id_admin)->delete()){
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

        $tokenDb = M_Admin::where('token', $token)->count();

        if($tokenDb > 0) {
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array =(array) $decoded;

            if($decoded_array['extime'] > time()) {
                $admin = M_Admin::get();

                $data = array();

                foreach($admin as $adm) {
                    $data[] = array(
                        'nama' => $adm->nama,
                        'email' => $adm->email,
                        'id_admin' => $adm->id_admin
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
