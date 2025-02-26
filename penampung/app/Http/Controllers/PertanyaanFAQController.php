<?php

namespace App\Http\Controllers;

use App\Models\UserFAQ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PertanyaanFAQController extends Controller
{
    //

    public function index ()
    {
        $user_faq = UserFAQ::where('delete_faq', 'N')->paginate(12);

       
        foreach ($user_faq as $data) {
            # code...
            if(str_contains($data->kantor, 'Kantor Pusat')){
                $kantor = DB::table('tb_bagian_kantor_pusat')->where('delete_bagian_kantor_pusat', 'N')->where('id_bagian_kantor_pusat', $data->id_bagian)->first();
                $data->nama_bagian  = $kantor->nama_bagian_kantor_pusat;
            }else if(str_contains($data->kantor, 'Kantor Cabang')){
                $kantor = DB::table('tb_bagian_kantor_cabang')->where('delete_bagian_kantor_cabang', 'N')->where('id_bagian_kantor_cabang', $data->id_bagian)->first();
                $data->nama_bagian = $kantor->nama_bagian_kantor_cabang;
            }else {
                $kantor = DB::table('tb_bagian_kantor_wilayah')->where('delete_bagian_kantor_wilayah', 'N')->where('id_bagian_kantor_wilayah', $data->id_bagian)->first();
                $data->nama_bagian = $kantor->nama_bagian_kantor_wilayah;
            }


        }

        return view('pages.pertanyaan.index', compact('user_faq'));
    }

    public function delete($id)
    {

        try {
            $user_faq = UserFAQ::where('id_faq', $id)->first();
            $user_faq->delete_faq = 'Y';
            $user_faq->deleted_by = auth()->user()->nama_pegawai;
            $user_faq->deleted_date = date('Y-m-d H:i:s');
            $user_faq->update();
            return back()->with('alert', 'success_Berhasil dihapus');
        } catch (\Throwable $th) {
            return back()->with('alert', 'danger_Gagal dihapus');
            //throw $th;
        }
    }
}
