<?php

namespace App\Http\Controllers;


use PDO;
use Image;
use Socialite;
use DataTables;
use Carbon\Carbon;
 
use App\Models\FAQ;
use App\Models\UserFAQ;
use App\Models\jawabanFAQ;
use App\Models\KategoriFAQ;
use Illuminate\Http\Request;
use App\Models\BagianKantorPusat;
use PhpParser\Node\Stmt\TryCatch;
use App\Models\BagianKantorCabang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\RoleAccount;
use Illuminate\Support\Facades\Session;
use App\Models\BagianKantorWilayah;

class FAQController extends Controller
{

    function __construct()
    {
        $this->role = new RoleAccountController();
        $this->route = "faq";
    }

    public function index()
    {
        $role = $this->role->role(Session::get('id_pegawai'), "", $this->route);


        if ($role->can_create == "Y") {
            if (auth()->user()->level_pegawai == 'Administrator') {
                # code...

                $input = '<a href="?kategori=data">
                <span class="badge badge-primary">
                <i class="bx bx-plus"></i> Tambah Kategori FAQ
                </span>
                </a>';
            } else {
                $input = '';
            }
        } else {
            $input = "";
        }
        $unit_kerja = ['Pusat', 'Cabang', 'Wilayah'];
        $kantor_pusat = DB::table('tb_kantor_pusat')
            ->where('delete_kantor_pusat', 'N')
            ->orderBy('nama_kantor_pusat', 'ASC')
            ->get();
        $kantor_cabang = DB::table('tb_kantor_cabang')
            ->where('delete_kantor_cabang', '=', 'N')
            ->orderBy('nama_kantor_cabang', 'ASC')
            ->get();

        $kantor_wilayah = DB::table('tb_kantor_wilayah')
            ->where('delete_kantor_wilayah', '=', 'N')
            ->orderBy('nama_kantor_wilayah', 'ASC')
            ->get();

        // $kategori = KategoriFAQ::where('id_kategori_faq', $id)->where('is_delete', 'N')->first();
        return view('pages.faq.index', compact('input', 'unit_kerja',  'kantor_pusat', 'kantor_cabang', 'kantor_wilayah'));
    }
    public function question($id)
    {
        $role = $this->role->role(Session::get('id_pegawai'), "", $this->route);

        if ($role->can_create == "Y") {
            if (auth()->user()->level_pegawai == 'Administrator') {

                $input = "<a href=" . route('faq.createQuest', $id) . ">
    <span class='badge badge-primary'>
    <i class='bx bx-plus'></i> Tambah Pertanyaan
    </span>
    </a>";
                # code...
            } else {
                $input = "";
                # code...
            }
        } else {
            $input = "";
        }
        $unit_kerja = ['Pusat', 'Cabang', 'Wilayah'];
        $kantor_pusat = DB::table('tb_kantor_pusat')
            ->where('delete_kantor_pusat', 'N')
            ->orderBy('nama_kantor_pusat', 'ASC')
            ->get();
        $kantor_cabang = DB::table('tb_kantor_cabang')
            ->where('delete_kantor_cabang', '=', 'N')
            ->orderBy('nama_kantor_cabang', 'ASC')
            ->get();

        $kantor_wilayah = DB::table('tb_kantor_wilayah')
            ->where('delete_kantor_wilayah', '=', 'N')
            ->orderBy('nama_kantor_wilayah', 'ASC')
            ->get();

        $kategori = KategoriFAQ::where('id_kategori_faq', $id)->where('is_delete', 'N')->first();

        return view('pages.faq.question', compact('input', 'id'));
    }

    public function pagination(Request $request)
    {
        return view('pages.faq.pagination')->render();
    }

    public function paginationQuest($id)
    {
        return view('pages.faq.pagination_question', compact('id'))->render();
    }

    public function save(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required',
            'nama' => 'required',
            'email' => 'required',
            'kantor' => 'required',
            'bagian' => 'required',
        ]);
        $date = Carbon::now();
        $bagian = $request->bagian;
        $kantor = explode(',', $request->kantor);
        $kantor = $kantor[0];
        if ($kantor == 'Pusat') {
            $bagian_kantor = BagianKantorPusat::with('headOffice')->where('id_bagian_kantor_pusat', $bagian)->where('delete_bagian_kantor_pusat', 'N')->first();
            $nama_kantor = $bagian_kantor->nama_kantor_pusat;
            $nama_bagian = $bagian_kantor->nama_bagian_kantor_pusat;
        } elseif ($kantor == 'Cabang') {
            $bagian_kantor = BagianKantorCabang::with('branchOffice')->where('id_bagian_kantor_cabang', $bagian)->where('delete_bagian_kantor_cabang', 'N')->first();
            $nama_kantor = $bagian_kantor->nama_kantor_cabang;
            $nama_bagian = $bagian_kantor->nama_bagian_kantor_cabang;
        } else {
            $bagian_kantor = BagianKantorWilayah::with('regionalOffice')->where('id_bagian_kantor_wilayah', $bagian)->where('delete_bagian_kantor_wilayah', 'N')->first();
            $nama_kantor = $bagian_kantor->nama_kantor_wilayah;
            $nama_bagian = $bagian_kantor->nama_bagian_kantor_wilayah;
        }
        try {
            $faq  = new UserFAQ();
            $faq->pertanyaan_faq = $request->pertanyaan;
            $faq->kantor =  'Kantor ' . $kantor;
            $faq->id_bagian = $request->bagian;
            $faq->nama_faq = $request->nama;
            $faq->email_faq = $request->email;
            // $faq->save();
            // $to_email = $request->email;
            // // $to_email = $data_get_pegawai->email_pegawai;
            // $data = array(
            //     'id_pengaduan' => 12,
            // );

            // Mail::send('pages.faq.email_jawaban', $data, function ($message) use ($to_email) {
            //     $message->to($to_email)
            //         ->subject('Pengaduan Baru (Pending)');
            //     $message->from('helpdesk@cnplus.id', 'Helpdesk');
            // });
            $from_email = $request->email;
            $to_email = 'hudaraden1@gmail.com';
            $data = [
                'pertanyaan' => $request->pertanyaan,
                'kantor' => 'Kantor ' . $kantor,
                'bagian_kantor' => $nama_kantor,
                'bagian' => $nama_bagian,
                'nama' => $request->nama,
                'emai' => $request->email,
                'date' => $date

            ];

            try {
                Mail::send('pages.faq.email_jawaban', $data, function ($message) use ($to_email, $from_email) {
                    $message->to($to_email)
                        ->subject('Pengaduan Baru (Pending)');
                    $message->from($from_email, 'Helpdesk');
                });

                // return response()->json(['status' => 'Email sent successfully']);
                return redirect()->route('faq')->with('alert', 'success_Berhasil Dikirim');
            } catch (\Exception $e) {
                Log::error('Email failed: ' . $e->getMessage());
                return redirect()->route('faq')->with('alert', 'danger_Email gagal dikirim'  . $e->getMessage());

                // return response()->json(['status' => 'Email failed to send', 'error' => $e->getMessage()], 500);
            }


            return redirect()->route('faq')->with('alert', 'success_Berhasil Dikirim');
        } catch (\Exception $th) {
            //throw $th;
            return  redirect()->back()->with('alert', 'danger_' . $th->getMessage())->withInput($request->all());
            //    return  redirect()->back()->with('alert', 'danger_Harap isi data dengan lengkap.')->withInput($request->all());
        }
    }
    // save
    // public function save(Request $request)
    // {
    //     $request->validate([
    //         'pertanyaan' => 'required',
    //         'nama' => 'required',
    //         'email' => 'required',
    //         'kantor' => 'required',
    //         'bagian' => 'required',
    //         'urutan' => 'required'
    //     ]);


    //     $kantor = explode(',', $request->kantor);
    //     $kantor = $kantor[0];
    //     try {
    //         $faq  = new FAQ();
    //         $faq->id_kategori_faq = $request->id_kategori;
    //         $faq->pertanyaan_faq = $request->pertanyaan;
    //         $faq->kantor =  'Kantor ' . $kantor;
    //         $faq->id_bagian = $request->bagian;
    //         $faq->nama_faq = $request->nama;
    //         $faq->email_faq = $request->email;
    //         $faq->urutan_faq =  $request->urutan;
    //         $faq->save();
    //         return redirect()->route('faq.question', $request->id_kategori);
    //     } catch (\Exception $th) {
    //         //throw $th;
    //         return  redirect()->back()->with('alert', 'danger_' . $th->getMessage())->withInput($request->all());
    //         //    return  redirect()->back()->with('alert', 'danger_Harap isi data dengan lengkap.')->withInput($request->all());
    //     }
    // }
    public function updateQuest(Request $request, $id)
    {

        if (empty($request->pertanyaan)) {
            return back()->with('alert', 'danger_Harap isi data dengan lengkap.');
        } else {

            try {
                $faq =  FAQ::where('id_faq', $id)->where('delete_faq', 'N')->first();
                $faq->id_kategori_faq = $request->id_kategori;
                $faq->pertanyaan_faq = $request->pertanyaan;
                $faq->jawaban_faq = $request->jawaban;
                $faq->updated_by = auth()->user()->nama_pegawai;
                $faq->update();
                return back()->with('alert', 'success_Berhasil Disimpan');
            } catch (\Exception $th) {
                //throw $th;
                return back()->with('alert', 'danger_' . $th->getMessage());
            }
        }

        // $request->validate([
        //     'pertanyaan' => 'required',
        //     'nama' => 'required',
        //     'email' => 'required',
        //     'kantor' => 'required',
        //     'bagian' => 'required',
        //     'urutan' => 'required'
        // ]);

        // // dd($request);
        // $kantor = explode(',', $request->kantor);
        // $kantor = $kantor[0];
        // try {
        //     $faq  =  FAQ::where('id_faq', $id)->where('delete_faq', 'N')->first();
        //     $faq->id_kategori_faq = $request->id_kategori;
        //     $faq->pertanyaan_faq = $request->pertanyaan;
        //     $faq->kantor =  'Kantor ' . $kantor;
        //     $faq->id_bagian = $request->bagian;
        //     $faq->nama_faq = $request->nama;
        //     $faq->email_faq = $request->email;
        //     $faq->urutan_faq =  $request->urutan;
        //     $faq->updated_by = auth()->user()->nama_pegawai;
        //     $faq->update();
        //     return redirect()->route('faq.question', $request->id_kategori);
        // } catch (\Exception $th) {
        //     //throw $th;
        //     return  redirect()->back()->with('alert', 'danger_' . $th->getMessage())->withInput($request->all());
        //     //    return  redirect()->back()->with('alert', 'danger_Harap isi data dengan lengkap.')->withInput($request->all());
        // }
    }
    public function saveCategory(Request $request)
    {
        // dd($request->kategori);
        if (empty($request->kategori)) {
            return back()->with('alert', 'danger_Harap isi data dengan lengkap.')->withInput($request->all());
        } else {

            try {
                $kategori = new KategoriFAQ();
                $kategori->nama_kategori_faq = $request->kategori;
                $kategori->created_by = auth()->user()->nama_pegawai;
                $kategori->save();

                return redirect()->route('faq');
            } catch (\Throwable $th) {

                return back()->with('alert', 'danger_Harap isi data dengan lengkap.')->withInput($request->all());

                //throw $th;
            }
        }
    }

    public function createQuestion($id)
    {

        $unit_kerja = ['Pusat', 'Cabang', 'Wilayah'];
        $kantor_pusat = DB::table('tb_kantor_pusat')
            ->where('delete_kantor_pusat', 'N')
            ->orderBy('nama_kantor_pusat', 'ASC')
            ->get();
        $kantor_cabang = DB::table('tb_kantor_cabang')
            ->where('delete_kantor_cabang', '=', 'N')
            ->orderBy('nama_kantor_cabang', 'ASC')
            ->get();

        $kantor_wilayah = DB::table('tb_kantor_wilayah')
            ->where('delete_kantor_wilayah', '=', 'N')
            ->orderBy('nama_kantor_wilayah', 'ASC')
            ->get();

        $kategori = KategoriFAQ::where('id_kategori_faq', $id)->where('is_delete', 'N')->first();
        return view('pages.faq.tambah_question', compact('unit_kerja', 'kategori',  'kantor_pusat', 'kantor_cabang', 'kantor_wilayah'));
    }

    public function saveQuest(Request $request)
    {
        if (empty($request->pertanyaan)) {
            return back()->with('alert', 'danger_Harap isi data dengan lengkap.');
        } else {

            try {
                $faq = new FAQ();
                $faq->id_kategori_faq = $request->id_kategori;
                $faq->pertanyaan_faq = $request->pertanyaan;
                $faq->jawaban_faq = $request->jawaban;
                $faq->created_by = auth()->user()->nama_pegawai;
                $faq->save();
                return back()->with('alert', 'success_Berhasil Disimpan');
            } catch (\Exception $th) {
                //throw $th;
                return back()->with('alert', 'danger_' . $th->getMessage());
            }
        }
    }
    public function editQuest($id)
    {
        $faq = FAQ::where('id_faq', $id)->where('delete_faq', 'N')->first();
        // $bagian_kantor_pusat = '';
        // $bagian_kantor_cabang = '';
        // $bagian_kantor_wilayah = '';
        // $kantors = explode(',', $faq->kantor);
        // $kantor = $kantors[0];
        // $kantor = explode(' ', $kantor);
        // $kantor = $kantor[1];


        // $unit_kerja = ['Pusat', 'Cabang', 'Wilayah'];
        // $kantor_pusat = DB::table('tb_kantor_pusat')
        //     ->where('delete_kantor_pusat', 'N')
        //     ->orderBy('nama_kantor_pusat', 'ASC')
        //     ->get();
        // $kantor_cabang = DB::table('tb_kantor_cabang')
        //     ->where('delete_kantor_cabang', '=', 'N')
        //     ->orderBy('nama_kantor_cabang', 'ASC')
        //     ->get();

        // $kantor_wilayah = DB::table('tb_kantor_wilayah')
        //     ->where('delete_kantor_wilayah', '=', 'N')
        //     ->orderBy('nama_kantor_wilayah', 'ASC')
        //     ->get();
        // if ($faq->kantor == 'Kantor Pusat') {
        //     $bagian_kantor_pusat = DB::table('tb_kantor_pusat')
        //         ->join('tb_bagian_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
        //         ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', $faq->id_bagian)
        //         ->where('tb_kantor_pusat.delete_kantor_pusat', '=', 'N')
        //         ->first();

        //     $id_unit_kerja = $kantor . ',' . $bagian_kantor_pusat->id_kantor_pusat;
        // } elseif ($faq->kantor == 'Kantor Cabang') {
        //     $bagian_kantor_cabang = DB::table('tb_kantor_cabang')
        //         ->join('tb_bagian_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
        //         ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', $faq->id_bagian)
        //         ->where('tb_kantor_cabang.delete_kantor_cabang', '=', 'N')
        //         ->first();
        //     $id_unit_kerja = $kantor . ',' . $bagian_kantor_cabang->id_kantor_cabang;
        // } else {
        //     $bagian_kantor_wilayah = DB::table('tb_kantor_wilayah')
        //         ->join('tb_bagian_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
        //         ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', $faq->id_bagian)
        //         ->where('tb_kantor_wilayah.delete_kantor_wilayah', '=', 'N')
        //         ->first();
        //     $id_unit_kerja = $kantor . ',' . $bagian_kantor_wilayah->id_bagian_kantor_wilayah;
        // }


        $kategori = KategoriFAQ::where('id_kategori_faq', $faq->id_kategori_faq)->where('is_delete', 'N')->first();
        return view('pages.faq.ubah_question', compact( 'faq', 'kategori'));
    }
    public function detailQuest($id)
    {
        $faq = FAQ::where('id_faq', $id)->where('delete_faq', 'N')->first();
        $bagian_kantor_pusat = '';
        $bagian_kantor_cabang = '';
        $bagian_kantor_wilayah = '';
        $kantors = explode(',', $faq->kantor);
        $kantor = $kantors[0];
        $kantor = explode(' ', $kantor);

        $kantor = $kantor[1];

        $jawaban = jawabanFAQ::where('id_faq', $faq->id_faq)->where('is_delete', 'N')->first();
        $unit_kerja = ['Pusat', 'Cabang', 'Wilayah'];
        $kantor_pusat = DB::table('tb_kantor_pusat')
            ->where('delete_kantor_pusat', 'N')
            ->orderBy('nama_kantor_pusat', 'ASC')
            ->get();
        $kantor_cabang = DB::table('tb_kantor_cabang')
            ->where('delete_kantor_cabang', '=', 'N')
            ->orderBy('nama_kantor_cabang', 'ASC')
            ->get();

        $kantor_wilayah = DB::table('tb_kantor_wilayah')
            ->where('delete_kantor_wilayah', '=', 'N')
            ->orderBy('nama_kantor_wilayah', 'ASC')
            ->get();
        if ($faq->kantor == 'Kantor Pusat') {
            $bagian_kantor_pusat = DB::table('tb_kantor_pusat')
                ->join('tb_bagian_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
                ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', $faq->id_bagian)
                ->where('tb_kantor_pusat.delete_kantor_pusat', '=', 'N')
                ->first();
            $nama_kantor = $bagian_kantor_pusat->nama_kantor_pusat;
            $nama_bagian = $bagian_kantor_pusat->nama_bagian_kantor_pusat;

            $id_unit_kerja = $kantor . ',' . $bagian_kantor_pusat->id_kantor_pusat;
        } elseif ($faq->kantor == 'Kantor Cabang') {
            $bagian_kantor_cabang = DB::table('tb_kantor_cabang')
                ->join('tb_bagian_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
                ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', $faq->id_bagian)
                ->where('tb_kantor_cabang.delete_kantor_cabang', '=', 'N')
                ->first();
            $nama_kantor = $bagian_kantor_cabang->nama_kantor_cabang;
            $nama_bagian = $bagian_kantor_cabang->nama_bagian_kantor_cabang;

            $id_unit_kerja = $kantor . ',' . $bagian_kantor_cabang->id_kantor_cabang;
        } else {
            $bagian_kantor_wilayah = DB::table('tb_kantor_wilayah')
                ->join('tb_bagian_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
                ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', $faq->id_bagian)
                ->where('tb_kantor_wilayah.delete_kantor_wilayah', '=', 'N')
                ->first();
            $nama_kantor = $bagian_kantor_wilayah->nama_kantor_wilayah;
            $nama_bagian = $bagian_kantor_wilayah->nama_bagian_kantor_wilayah;
            $id_unit_kerja = $kantor . ',' . $bagian_kantor_wilayah->id_bagian_kantor_wilayah;
        }


        $kategori = KategoriFAQ::where('id_kategori_faq', $faq->id_kategori_faq)->where('is_delete', 'N')->first();
        return view('pages.faq.detail_question', compact('unit_kerja', 'faq', 'jawaban', 'kategori', 'kantor_pusat', 'kantor_cabang', 'kantor_wilayah', 'id_unit_kerja', 'bagian_kantor_pusat', 'bagian_kantor_cabang', 'bagian_kantor_wilayah', 'nama_kantor', 'nama_bagian'));
    }

    public function saveAnswer(Request $request)
    {
        if (empty($request->jawaban)) {
            return back()->with('alert', 'danger_Harap isi data dengan lengkap.');
        } else {

            try {
                $jawaban = new jawabanFAQ();
                $jawaban->id_faq = $request->id_faq;
                $jawaban->jawaban = $request->jawaban;
                $jawaban->created_by = auth()->user()->nama_pegawai;
                $jawaban->save();
                return back()->with('alert', 'success_Berhasil Disimpan');
            } catch (\Exception $th) {
                //throw $th;
                return back()->with('alert', 'danger_' . $th->getMessage());
            }
        }
    }

    public function update(Request $request,$id)
    {
        if (empty($request->kategori)) {
            return back()->with('alert', 'danger_Harap isi data dengan lengkap.')->withInput($request->all());
        } else {

            try {
                $kategori = KategoriFAQ::where('id_kategori_faq', $id)->where('is_delete', 'N')->first();
                $kategori->nama_kategori_faq = $request->kategori;
                $kategori->updated_by = auth()->user()->nama_pegawai;
                $kategori->update();

                return redirect()->route('faq');
            } catch (\Throwable $th) {

                return back()->with('alert', 'danger_Harap isi data dengan lengkap.')->withInput($request->all());

                //throw $th;
            }
        }
    }

    public function delete(Request $request)
    {
        $id = $request->delete;
        $where = array(
            'id_faq' => $id,
            'delete_faq' => 'N',
        );
        $values = array(
            'delete_faq' => 'Y',
        );
        DB::table('tb_faq')->where($where)->update($values);
        return back();
    }

    public function deleteQuest(Request $request)
    {
        
        $id = $request->delete;
        $date = Carbon::now();
        try {
            //code...
            $faq = FAQ::where('id_faq', $id)->first();
            $faq->delete_faq = 'Y';
            $faq->deleted_by = auth()->user()->nama_pegawai;
            $faq->deleted_date = $date;
            $faq->update();

            // $jawaban = jawabanFAQ::where('id_faq', $id)->first();
            // $jawaban->deleted_by = auth()->user()->nama_pegawai;
            // $jawaban->deleted_date = $date;
            // $jawaban->is_delete = 'Y';
            // $jawaban->update();
            // return redirect()->route('faq.question', $faq->id_kategori_faq);
            return back();
        } catch (\Exception $th) {
            //throw $th;
  
            return back();
        }
    }
}
