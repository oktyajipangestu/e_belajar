<?php

namespace App\Http\Controllers;

use App\M_Jawaban;
use App\M_Pelajar;
use App\M_Pengajar;
use App\M_Skor;
use App\M_Soal;
use Illuminate\Http\Request;
use \Firebase\JWT\JWT;

class Ujian extends Controller
{
    public function tambahSoal(Request $request) {
        $validator = Validator::make($request ->all(), [
            'id_kelas' => 'required',
            'pertanyaan' => 'required | unique:tbl_soal',
            'opsi1' => 'required',
            'opsi2' => 'required',
            'opsi3' => 'required',
            'opsi4' => 'required',
            'jawaban' => 'required'
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
                if(M_Soal::create([
                    'id_kelas' => $request->id_kelas,
                    'pertanyaan' => $request->pertanyaan,
                    'opsi1' => $request->opsi1,
                    'opsi2' => $request->opsi2,
                    'opsi3' => $request->opsi3,
                    'opsi4' => $request->opsi4,
                    'jawaban' => $request->jawaban
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

    public function hapusSoal(Request $request) {
        $validator = Validator::make($request->all(), [
            'id_soal' => 'required',
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
                if(M_Soal::where('id_soal', $request->id_soal)->delete()){
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

    public function listSoal(Request $request) {
        $token = $request->token;
        $tokenDB = M_Pelajar::where('token', $token)->count();
        $tokenPengajar = M_Pengajar::where('token', $token)->count();

        if($tokenDB > 0) {
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array =(array) $decoded;

            if($decoded_array['extime'] > time()) {
                $cal_skor = M_Skor::where('id_pelajar', $decoded_array['id_pelajar'])->where('id_kelas', $request->id_kelas)->where('status','1')->count();
                $id_s = "";
                if ($cal_skor > 0) {
                    $id_s = M_Skor::where('id_pelajar', $decoded_array['id_pelajar'])->where('id_kelas', $request->id_kelas)->where('status', '1')->first();
                } else {
                    M_Skor::create([
                        'id_pelajar' => $decoded_array['id_pelajar'],
                        'id_kelas' => $request->id_kelas
                    ]);
                    $id_s = M_Skor::where('id_pelajar', $decoded_array['id_pelajar'])->where('id_kelas', $request->id_kelas)->where('status', '1')->first();
                }

                $skor = M_Skor::where('id_pelajar', $decoded_array['id_pelajar'])->where('id_kelas', $request->id_kelas)->where('status','1')->first();

                $jawaban = M_Jawaban::where('id_pelajar', $decoded_array['id_pelajar'])->where('id_kelas', $request->id_kelas)->first();
                $jml_jawaban = M_Jawaban::where('id_pelajar', $decoded_array['id_pelajar'])->where('id_kelas', $request->id_kelas)->where('id_skor', $skor->id_skor)->count();

                $jumlah_soal = M_Soal::count();
                $max_rand = $jumlah_soal - 10;
                $mulai = rand(0, $max_rand);
                $soal = M_Soal::skip($mulai)->take(10)->get();

                $data = array();
                foreach($soal as $p) {
                    $data[] = array(
                        'id_soal' => $p->id_soal,
                        'id_kelas' => $p->id_kelas,
                        'pertanyaan' => $p->pertanyaan,
                        'opsi1' => $p->opsi1,
                        'opsi2' => $p->opsi2,
                        'opsi3' => $p->opsi3,
                        'opsi4' => $p->opsi4,
                        'jumlah_jawaban' => $jml_jawaban,
                    );
                }

                return response()->json([
                    'status' => 'berhasil',
                    'message' => 'Data berhasil diambil',
                    'id_skor' => $id_s->id_skor,
                    'data' => $data
                ]);
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

    public function jawab(Request $request) {
        $token = $request->token;

        $tokenDB = M_Pelajar::where('token', $token)->count();

        if($tokenDB > 0) {
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array =(array) $decoded;

            if($decoded_array['extime'] > time()) {
                $soal = M_Soal::where('id_soal', $request->id_soal)->get();

                foreach($soal as $p) {
                    if($request->jawaban == $p->jawaban) {
                        if(M_Jawaban::create([
                            'id_pelajar' => $decoded_array['id_pelajar'],
                            'id_kelas' => $request->id_kelas,
                            'id_soal' => $p->id_soal,
                            'jawaban'=> $request->jawaban,
                            'id_skor' => $request->id_skor,
                            'status_jawaban' => '1'
                        ])) {
                            return response()->json([
                                'status' => 'berhasil',
                                'message' => 'Data berhasil disimpan'
                            ]);
                        } else {
                            return response()->json([
                                'status' => 'berhasil',
                                'message' => 'Data gagal disimpan'
                            ]);
                        }
                    } else {
                        if(M_Jawaban::create([
                            'id_pelajar' => $decoded_array['id_pelajar'],
                            'id_kelas' => $request->id_kelas,
                            'id_soal' => $p->id_soal,
                            'jawaban'=> $request->jawaban,
                            'id_skor' => $request->id_skor,
                            'status_jawaban' => '0'
                        ])) {
                            return response()->json([
                                'status' => 'berhasil',
                                'message' => 'Data berhasil disimpan'
                            ]);
                        } else {
                            return response()->json([
                                'status' => 'berhasil',
                                'message' => 'Data gagal disimpan'
                            ]);
                        }
                    }
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

    public function hitungSkor(Request $request) {
        $token = $request->token;
        $tokenDB = M_Pelajar::where('token', $token)->count();

        if($tokenDB > 0) {
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array =(array) $decoded;

            if($decoded_array['extime'] > time()) {
                $id_s = M_Skor::where('id_pelajar', $decoded_array['id_pelajar'])->where('id_kelas', $request->id_kelas)->where('status', '1')->first();
                $jawaban = M_Jawaban::where('status_jawaban', '1')->where('id_skor', $id_s->id_skor)->count();

                return response()->json([
                    'status' => 'berhasil',
                    'skor' => $jawaban
                ]);
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

    public function selesaiUjian(Request $request) {
        $token = $request->token;
        $tokenDb = M_Pelajar::where('token', $token)->count();

        if($tokenDb > 0) {
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array =(array) $decoded;

            if($decoded_array['extime'] > time()) {
                $id_s = M_Skor::where('id_pelajar', $decoded_array['id_pelajar'])->where('id_kelas', $request->id_kelas)->where('status', '1')->first();

                if(M_Skor::where('id_skor', $id_s->id_skor)->update([
                    'status' => 0
                ])) {
                    return response()->json([
                        'status' => 'berhasil',
                        'message' => 'Data berhasil diubah',
                        'data' => $request->id_kelas

                    ]);
                } else {
                    return response()->json([
                        'status' => 'gagal',
                        'message' => 'Data gagal diubah'
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
}
