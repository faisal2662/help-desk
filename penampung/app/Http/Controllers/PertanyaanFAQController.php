<?php

namespace App\Http\Controllers;

use App\Models\UserFAQ;
use Illuminate\Http\Request;

class PertanyaanFAQController extends Controller
{
    //

    public function index ()
    {
        $user_faq = UserFAQ::where('delete_faq', 'N')->orderBy('tgl_faq', 'desc')->paginate(12);
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
 