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

}
