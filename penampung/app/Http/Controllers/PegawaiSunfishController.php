<?php

namespace App\Http\Controllers;

use DateTime;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PegawaiSunfishController extends Controller
{
    //

    public function index()
    {
        return view('pages.pegawai_sunfish.index');
    }

    public function datatables()
    {
        $pegawai = DB::table('tb_pegawai_sunfish')->where('delete_pegawai', 'N')->orderBy('created_date', 'desc')->get();

        $no = 1;
        foreach ($pegawai as $data) {
            # code...

            $data->no = $no++;
            $data->action = ' <a href="pegawai-sunfish/show/' . $data->id_pegawai_sunfish . '" class="badge text-white bg-info"><i class="bx bx-search-alt-2"></i> Lihat</a>';
            $data->email_pegawai = $this->decryptssl($data->email, 'P/zqOYfEDWHmQ9/g8PrApw==');
            $data->telp_pegawai = $this->decryptssl($data->primary_phone, 'P/zqOYfEDWHmQ9/g8PrApw==');
        }

        return DataTables::of($pegawai)->escapecolumns([])->make(true);
    }
    private function decryptssl($str, $key)
    {
        $str = base64_decode($str);
        $key = base64_decode($key);
        $decrypted = openssl_decrypt($str, 'AES-128-ECB', $key,  OPENSSL_RAW_DATA);
        return $decrypted;
    }
    public function show($id)
    {
        $pegawai = DB::table('tb_pegawai_sunfish')->where('delete_pegawai', 'N')->where('id_pegawai_sunfish', $id)->first();

        $pegawai->email = $this->decryptssl($pegawai->email, 'P/zqOYfEDWHmQ9/g8PrApw==');
        $pegawai->primary_phone = $this->decryptssl($pegawai->primary_phone, 'P/zqOYfEDWHmQ9/g8PrApw==');
        $pegawai->primary_address = $this->decryptssl($pegawai->primary_address, 'P/zqOYfEDWHmQ9/g8PrApw==');
        $pegawai->birthday = $this->decryptssl($pegawai->birthday, 'P/zqOYfEDWHmQ9/g8PrApw==');
        // $pegawai->birthday = date_format($pegawai->birthday, 'd-m-Y');

        // Menghapus bagian {ts ' '} menggunakan str_replace
        $cleanDate = str_replace(["{ts '", "'}"], '', $pegawai->birthday);
        $date = new DateTime($cleanDate);
        $pegawai->birthday = $date->format('d-m-Y');
        return view('pages.pegawai_sunfish.show', compact('pegawai'));
    }


    public function lastSync()
    {
        $pegawai = DB::table('tb_pegawai_sunfish')->where('delete_pegawai', 'N')->orderBy('updated_date')->first();
        $date = new DateTime($pegawai->updated_date);
        $date = $date->format('l, d-m-Y');
        return response()->json(['data' => $date]);
    }

    public function syncData(Request $request)
    {
        $result = $request->data;

        try {

            foreach ($result as $pegawai) {
                # code...

                $get_pegawai = DB::table('tb_pegawai_sunfish')->where('delete_pegawai', 'N')->where('employee_id', $pegawai['EMPLOYEE_ID'])->first();

                if (!is_null($get_pegawai)) {
                    DB::table('tb_pegawai_sunfish')->where('employee_id', $pegawai['EMPLOYEE_ID'])->update($pegawai);
                } else {
                    DB::table('tb_pegawai_sunfish')->insert($pegawai);
                }
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['status' => 'gagal : ' . $th->getMessages]);
        }
    }
}
