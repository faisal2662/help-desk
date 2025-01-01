<?php

namespace App\Http\Controllers;


use Image;
use Session;
use Socialite;
use DataTables;
use App\SMTP_Send;
use Carbon\Carbon;
use App\Models\Pengaduan;
use App\Models\Approved;
use App\Models\Checked;

use App\Models\Pegawai;
use App\Models\KepalaUnit;
use App\Models\KantorPusat;
use App\Models\KantorCabang;
use App\Models\BagianKantorPusat;
use App\Models\BagianKantorCabang;
use App\Models\BagianKantorWilayah;
use Illuminate\Http\Request;

//Model
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PengaduanController extends Controller
{
    function __construct()
    {
        $this->role = new RoleAccountController();
        $this->route = "pengaduan";
    }

    public function index()
    {
        $role = $this->role->role(auth()->user()->id_pegawai, "", $this->route);

        // dd($role);
          
        if ($role->can_create == "Y") {
            if (auth()->user()->level_pegawai == 'Staff' && auth()->user()->sebagai_pegawai == 'PIC') {
                $input = '<a href="pengaduan/buat">
							<span class="badge badge-primary">
							  <i class="bx bx-plus"></i> Buat Pengaduan Baru
							</span>
						</a>';
            } else {
                $input = '';
            }
        } else {
            $input = "";
        }

        return view('pages.pengaduan.index', compact('input'));
    }

    public function create()
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
        $bagian_kantor_pusat = DB::table('tb_bagian_kantor_pusat')
            ->join('tb_kantor_pusat', 'tb_kantor_pusat.id_kantor_pusat', '=', 'tb_bagian_kantor_pusat.id_kantor_pusat')
            ->where('tb_bagian_kantor_pusat.delete_bagian_kantor_pusat', '=', 'N')
            ->orderBy('tb_bagian_kantor_pusat.nama_bagian_kantor_pusat', 'ASC')
            ->get();


        $bagian_kantor_cabang = DB::table('tb_bagian_kantor_cabang')
            ->join('tb_kantor_cabang', 'tb_kantor_cabang.id_kantor_cabang', '=', 'tb_bagian_kantor_cabang.id_kantor_cabang')
            ->where('tb_bagian_kantor_cabang.delete_bagian_kantor_cabang', '=', 'N')
            ->orderBy('tb_bagian_kantor_cabang.nama_bagian_kantor_cabang', 'ASC')
            ->get();


        $bagian_kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
            ->join('tb_kantor_wilayah', 'tb_kantor_wilayah.id_kantor_wilayah', '=', 'tb_bagian_kantor_wilayah.id_kantor_wilayah')
            ->where('tb_bagian_kantor_wilayah.delete_bagian_kantor_wilayah', '=', 'N')
            ->orderBy('tb_bagian_kantor_wilayah.nama_bagian_kantor_wilayah', 'ASC')
            ->get();

        return view('pages.pengaduan.tambah', compact('unit_kerja', 'kantor_pusat', 'kantor_cabang', 'kantor_wilayah','bagian_kantor_pusat', 'bagian_kantor_cabang', 'bagian_kantor_wilayah'));
    }

    public function pagination (Request $request){
    	return view('pages.pengaduan.pagination')->render();
    }

    public function data_grid(Request $request)
    {
         
            $session_pegawai = DB::table('tb_pegawai')
            ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', auth()->user()->id_pegawai]])
            ->get();
        if ($session_pegawai->count() > 0) {
            foreach ($session_pegawai as $data_session_pegawai) {
    
                if ($data_session_pegawai->level_pegawai == 'Administrator') {
                    if ($_GET['filter'] == 'Semua') {
                        if (isset($_GET['search'])) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->whereRaw(
                                    '
                                tb_pengaduan.nama_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                tb_pengaduan.status_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%"
                            '
                                )
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->paginate(12);
                        } else {
                            $pengaduan = DB::table('tb_pengaduan')->where('tb_pengaduan.delete_pengaduan', '=', 'N')->orderBy('tb_pengaduan.id_pengaduan', 'DESC')->paginate(12);
                        }
                    } else {
                        if (isset($_GET['search'])) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                ->whereRaw(
                                    '
                                tb_pengaduan.nama_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                tb_pengaduan.status_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%"
                            '
                                )
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->paginate(12);
                        } else {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->paginate(12);
                        }
                    }
                } elseif ($data_session_pegawai->level_pegawai == 'Staff') {
                    if ($_GET['filter'] == 'Semua') {
                        if (isset($_GET['search'])) {
                            if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->whereRaw(
                                        '
                                    tb_pengaduan.nama_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.status_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%"
                                '
                                    )
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->whereRaw(
                                        '
                                    tb_pengaduan.nama_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.status_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%"
                                '
                                    )
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            } else {
                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->whereRaw(
                                        '
                                    tb_pengaduan.nama_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.status_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%"
                                '
                                    )
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            }
                            // $pengaduan = DB::table('tb_pengaduan')
                            //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                            //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                            //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                            //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                            //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            //     ->whereRaw(
                            //         '
                            //     tb_pengaduan.nama_pengaduan LIKE "%' .
                            //             $_GET['search'] .
                            //             '%" Or
                            //     tb_pengaduan.keterangan_pengaduan LIKE "%' .
                            //             $_GET['search'] .
                            //             '%" Or
                            //     tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                            //             $_GET['search'] .
                            //             '%" Or
                            //     tb_pengaduan.status_pengaduan LIKE "%' .
                            //             $_GET['search'] .
                            //             '%"
                            // ',
                            //     )
                            //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            //     ->paginate(12);
                        } else {
                            if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            } else {
                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            }
                      
                        }
                    } else {
                        if (isset($_GET['search'])) {
                            if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->whereRaw(
                                        '
                                    tb_pengaduan.nama_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.status_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%"
                                ',
                                    )
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->whereRaw(
                                        '
                                    tb_pengaduan.nama_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.status_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%"
                                ',
                                    )
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            } else {
                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->whereRaw(
                                        '
                                    tb_pengaduan.nama_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.status_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%"
                                ',
                                    )
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            }
                            // $pengaduan = DB::table('tb_pengaduan')
                            //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                            //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                            //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                            //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                            //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            //     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                            //     ->whereRaw(
                            //         '
                            //     tb_pengaduan.nama_pengaduan LIKE "%' .
                            //             $_GET['search'] .
                            //             '%" Or
                            //     tb_pengaduan.keterangan_pengaduan LIKE "%' .
                            //             $_GET['search'] .
                            //             '%" Or
                            //     tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                            //             $_GET['search'] .
                            //             '%" Or
                            //     tb_pengaduan.status_pengaduan LIKE "%' .
                            //             $_GET['search'] .
                            //             '%"
                            // ',
                            //     )
                            //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            //     ->paginate(12);
                        } else {
                            if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            } else {
                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            }
                            // $pengaduan = DB::table('tb_pengaduan')
                            //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                            //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                            //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                            //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                            //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            //     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                            //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            //     ->paginate(12);
                        }
                    }
                }
            
                elseif ($data_session_pegawai->level_pegawai == 'Kepala Bagian Unit Kerja') {

                    if ($_GET['filter'] == 'Semua') {
                        if (isset($_GET['search'])) {
                            if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->whereRaw(
                                        '
                                    tb_pengaduan.nama_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.status_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%"
                                ',
                                    )
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->whereRaw(
                                        '
                                    tb_pengaduan.nama_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.status_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%"
                                ',
                                    )
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                                // dd($data_session_pegawai->id_bagian_kantor_cabang);
                            } else {
                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->whereRaw(
                                        '
                                    tb_pengaduan.nama_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.status_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%"
                                ',
                                    )
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            }
                            // $pengaduan = DB::table('tb_pengaduan')
                            //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                            //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                            //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                            //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                            //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            //     ->whereRaw(
                            //         '
                            //     tb_pengaduan.nama_pengaduan LIKE "%' .
                            //             $_GET['search'] .
                            //             '%" Or
                            //     tb_pengaduan.keterangan_pengaduan LIKE "%' .
                            //             $_GET['search'] .
                            //             '%" Or
                            //     tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                            //             $_GET['search'] .
                            //             '%" Or
                            //     tb_pengaduan.status_pengaduan LIKE "%' .
                            //             $_GET['search'] .
                            //             '%"
                            // ',
                            //     )
                            //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            //     ->paginate(12);
                        } else {
                            if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                                // dd($data_session_pegawai->id_bagian_kantor_cabang);
                            } else {
                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            }
                        }
                    } else {
                        if (isset($_GET['search'])) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                ->whereRaw(
                                    '
                                tb_pengaduan.nama_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                tb_pengaduan.status_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%"
                            ',
                                )
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->paginate(12);
                        } else {
                            if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')

                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            } else {
                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            }

                            // $pengaduan = DB::table('tb_pengaduan')
                            //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                            //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                            //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                            //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                            //     ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                            //     ->where('tb_pengaduan.status_pengaduan', '!=', 'Checked')
                            //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            //     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                            //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            //     ->paginate(12);
                        }
                    }
                    
                }
                  elseif ($data_session_pegawai->level_pegawai == 'Kepala Unit Kerja') {

                    if ($_GET['filter'] == 'Semua') {

                        if (isset($_GET['search'])) {

                            if ($data_session_pegawai->kantor_pegawai == 'Kantor Pusat') {
                                $kepala_unit = KepalaUnit::with('BagianKantorPusat')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja','N' )->first();

                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_pusat)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->whereRaw(
                                        '
                                    tb_pengaduan.nama_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.status_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%"
                                ',
                                    )
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            } elseif ($data_session_pegawai->kantor_pegawai == 'Kantor Cabang') {
                                $kepala_unit = KepalaUnit::with('BagianKantorCabang')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja','N' )->first();

                                $pengaduan = Pengaduan::with(['BagianKantorCabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_cabang)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->whereRaw(
                                        '
                                    tb_pengaduan.nama_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.status_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%"
                                ',
                                    )
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                                // dd($data_session_pegawai->id_bagian_kantor_cabang);
                            } else {
                                $kepala_unit = KepalaUnit::with('BagianKantorWilayah')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja','N' )->first();

                                $pengaduan = Pengaduan::with(['BagianKantorWilayah'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_wilayah)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->whereRaw(
                                        '
                                    tb_pengaduan.nama_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%" Or
                                    tb_pengaduan.status_pengaduan LIKE "%' .
                                            $_GET['search'] .
                                            '%"
                                ',
                                    )
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            }
                            // $pengaduan = DB::table('tb_pengaduan')
                            //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                            //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                            //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                            //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                            //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            //     ->whereRaw(
                            //         '
                            //     tb_pengaduan.nama_pengaduan LIKE "%' .
                            //             $_GET['search'] .
                            //             '%" Or
                            //     tb_pengaduan.keterangan_pengaduan LIKE "%' .
                            //             $_GET['search'] .
                            //             '%" Or
                            //     tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                            //             $_GET['search'] .
                            //             '%" Or
                            //     tb_pengaduan.status_pengaduan LIKE "%' .
                            //             $_GET['search'] .
                            //             '%"
                            // ',
                            //     )
                            //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            //     ->paginate(12);
                        } else {
                            // $session_pegawai = Pegawai::where('id_pegawai', auth()->user()->id_pegawai)->where('delete_pegawai', 'N')->first();
                            // dd($data_session_pegawai);
                            if ($data_session_pegawai->kantor_pegawai == 'Kantor Pusat') {
                                $kepala_unit = KepalaUnit::with('BagianKantorPusat')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja','N' )->first();
                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_pusat)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->paginate(12);
                            } elseif ($data_session_pegawai->kantor_pegawai == 'Kantor Cabang') {
                                $kepala_unit = KepalaUnit::with('BagianKantorPusat')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja','N' )->first();

                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_cabang)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->paginate(12);
                                // dd($pengaduan);
                            } else {
                                $kepala_unit = KepalaUnit::with('BagianKantorPusat')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja','N' )->first();
                                $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_wilayah)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            }
                        }
                    } else {
                        if (isset($_GET['search'])) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                ->whereRaw(
                                    '
                                tb_pengaduan.nama_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                tb_pengaduan.status_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%"
                            ',
                                )
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->paginate(12);
                        } else {
                            if ($data_session_pegawai->kantor_pegawai == 'Kantor Pusat') {
                                $kepala_unit = KepalaUnit::with('BagianKantorPusat')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja','N' )->first();

                                $pengaduan = Pengaduan::with(['BagianKantorPusat'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_pusat)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')

                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            } elseif ($data_session_pegawai->kantor_pegawai == 'Kantor Cabang') {
                                $kepala_unit = KepalaUnit::with('BagianKantorCabang')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja','N' )->first();

                                $pengaduan = Pengaduan::with(['BagianKantorCabang'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_cabang)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            } else {
                                $kepala_unit = KepalaUnit::with('BagianKantorWilayah')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja','N' )->first();

                                $pengaduan = Pengaduan::with(['BagianKantorWilayah'])
                                    ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_wilayah)
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->paginate(12);
                            }

                            // $pengaduan = DB::table('tb_pengaduan')
                            //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                            //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                            //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                            //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                            //     ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                            //     ->where('tb_pengaduan.status_pengaduan', '!=', 'Checked')
                            //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            //     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                            //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            //     ->paginate(12);
                        }
                    }
                }
            }
        }
        return view('pages.pengaduan.data_grid', compact('pengaduan', 'data_session_pegawai'))->render();
        // return view('pages.pengaduan.data_grid')->render();
    }

//     public function datatables(Request $request)
//     {

//         $status_klasifikasi = array(
//             'High' => 'danger',
//             'Medium' => 'warning',
//             'Low' => 'info',
//         );

//         $session_pegawai = DB::table('tb_pegawai')
//             ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', auth()->user()->id_pegawai]])
//             ->get();

//         if ($session_pegawai->count() < 1) {
//             return 0;
//         } else {

//             foreach ($session_pegawai as $data_session_pegawai);
//   if ($data_session_pegawai->level_pegawai == 'Administrator') {
//                 if ($_GET['filter'] == 'Semua') {
//                     if (isset($_GET['search'])) {
//                         $pengaduan = DB::table('tb_pengaduan')
//                             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                             ->whereRaw(
//                                 '
//                                     tb_pengaduan.nama_pengaduan LIKE "%' .
//                                     $_GET['search'] .
//                                     '%" Or
//                                     tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                                     $_GET['search'] .
//                                     '%" Or
//                                     tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                                     $_GET['search'] .
//                                     '%" Or
//                                     tb_pengaduan.status_pengaduan LIKE "%' .
//                                     $_GET['search'] .
//                                     '%"
//                                 ',
//                             )
//                             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                             ->get();
//                     } else {
//                         $pengaduan = DB::table('tb_pengaduan')->where('tb_pengaduan.delete_pengaduan', '=', 'N')->orderBy('tb_pengaduan.id_pengaduan', 'DESC')->paginate(12);
//                     }
//                 } else {
//                     if (isset($_GET['search'])) {
//                         $pengaduan = DB::table('tb_pengaduan')
//                             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                             ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                             ->whereRaw(
//                                 '
//                                     tb_pengaduan.nama_pengaduan LIKE "%' .
//                                     $_GET['search'] .
//                                     '%" Or
//                                     tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                                     $_GET['search'] .
//                                     '%" Or
//                                     tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                                     $_GET['search'] .
//                                     '%" Or
//                                     tb_pengaduan.status_pengaduan LIKE "%' .
//                                     $_GET['search'] .
//                                     '%"
//                                 ',
//                             )
//                             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                             ->get();
//                     } else {
//                         $pengaduan = DB::table('tb_pengaduan')
//                             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                             ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                             ->get();
//                     }
//                 }
//             } elseif ($data_session_pegawai->level_pegawai == 'Staff') {

//                 if ($_GET['filter'] == 'Semua') {
//                     if (isset($_GET['search'])) {
//                         if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
//                             $pengaduan = Pengaduan::with(['BagianKantorcabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->whereRaw(
//                                     '
//                                         tb_pengaduan.nama_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.status_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%"
//                                     ',
//                                 )
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

//                             $pengaduan = Pengaduan::with(['BagianKantorcabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->whereRaw(
//                                     '
//                                         tb_pengaduan.nama_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.status_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%"
//                                     ',
//                                 )
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         } else {
//                             $pengaduan = Pengaduan::with(['BagianKantorcabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->whereRaw(
//                                     '
//                                         tb_pengaduan.nama_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.status_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%"
//                                     ',
//                                 )
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         }
//                         // $pengaduan = DB::table('tb_pengaduan')
//                         //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                         //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                         //     ->whereRaw(
//                         //         '
//                         //     tb_pengaduan.nama_pengaduan LIKE "%' .
//                         //             $_GET['search'] .
//                         //             '%" Or
//                         //     tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                         //             $_GET['search'] .
//                         //             '%" Or
//                         //     tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                         //             $_GET['search'] .
//                         //             '%" Or
//                         //     tb_pengaduan.status_pengaduan LIKE "%' .
//                         //             $_GET['search'] .
//                         //             '%"
//                         // ',
//                         //     )
//                         //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                         //     ->paginate(12);
//                     } else {
//                         if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
//                             $pengaduan = Pengaduan::with(['BagianKantorPusat'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

//                             $pengaduan = Pengaduan::with(['BagianKantorCabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         } else {
//                             $pengaduan = Pengaduan::with(['BagianKantorWilayah'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         }
//                     }
//                 } else {
//                     if (isset($_GET['search'])) {
//                         if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
//                             $pengaduan = Pengaduan::with(['BagianKantorPusat'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->whereRaw(
//                                     '
//                                         tb_pengaduan.nama_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.status_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%"
//                                     ',
//                                 )
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

//                             $pengaduan = Pengaduan::with(['BagianKantorCabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->whereRaw(
//                                     '
//                                         tb_pengaduan.nama_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.status_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%"
//                                     ',
//                                 )
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         } else {
//                             $pengaduan = Pengaduan::with(['BagianKantorWilayah'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->whereRaw(
//                                     '
//                                         tb_pengaduan.nama_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.status_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%"
//                                     ',
//                                 )
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         }
//                         // $pengaduan = DB::table('tb_pengaduan')
//                         //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                         //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                         //     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                         //     ->whereRaw(
//                         //         '
//                         //     tb_pengaduan.nama_pengaduan LIKE "%' .
//                         //             $_GET['search'] .
//                         //             '%" Or
//                         //     tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                         //             $_GET['search'] .
//                         //             '%" Or
//                         //     tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                         //             $_GET['search'] .
//                         //             '%" Or
//                         //     tb_pengaduan.status_pengaduan LIKE "%' .
//                         //             $_GET['search'] .
//                         //             '%"
//                         // ',
//                         //     )
//                         //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                         //     ->paginate(12);
//                     } else {

//                         if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
//                             $pengaduan = Pengaduan::with(['BagianKantorPusat'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

//                             $pengaduan = Pengaduan::with(['BagianKantorCabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         } else {
//                             $pengaduan = Pengaduan::with(['BagianKantorWilayah'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         }
//                         // $pengaduan = DB::table('tb_pengaduan')
//                         //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                         //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                         //     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                         //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                         //     ->paginate(12);

//                     }
//                 }
//             } elseif ($data_session_pegawai->level_pegawai == 'Kepala Bagian Unit Kerja') {

//                 if ($_GET['filter'] == 'Semua') {
//                     if (isset($_GET['search'])) {
//                         if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
//                             $pengaduan = Pengaduan::with(['BagianKantorcabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->whereRaw(
//                                     '
//                                         tb_pengaduan.nama_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.status_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%"
//                                     ',
//                                 )
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

//                             $pengaduan = Pengaduan::with(['BagianKantorcabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->whereRaw(
//                                     '
//                                         tb_pengaduan.nama_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.status_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%"
//                                     ',
//                                 )
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();

//                             // dd($data_session_pegawai->id_bagian_kantor_cabang);
//                         } else {
//                             $pengaduan = Pengaduan::with(['BagianKantorcabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->whereRaw(
//                                     '
//                                         tb_pengaduan.nama_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.status_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%"
//                                     ',
//                                 )
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         }
//                         // $pengaduan = DB::table('tb_pengaduan')
//                         //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                         //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                         //     ->whereRaw(
//                         //         '
//                         //     tb_pengaduan.nama_pengaduan LIKE "%' .
//                         //             $_GET['search'] .
//                         //             '%" Or
//                         //     tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                         //             $_GET['search'] .
//                         //             '%" Or
//                         //     tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                         //             $_GET['search'] .
//                         //             '%" Or
//                         //     tb_pengaduan.status_pengaduan LIKE "%' .
//                         //             $_GET['search'] .
//                         //             '%"
//                         // ',
//                         //     )
//                         //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                         //     ->paginate(12);
//                     } else {
//                         if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
//                             $pengaduan = Pengaduan::with(['BagianKantorcabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

//                             $pengaduan = Pengaduan::with(['BagianKantorcabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();

//                             // dd($data_session_pegawai->id_bagian_kantor_cabang);
//                         } else {
//                             $pengaduan = Pengaduan::with(['BagianKantorcabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         }
//                     }
//                 } else {
//                     if (isset($_GET['search'])) {
//                         $pengaduan = DB::table('tb_pengaduan')
//                             ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                             ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                             ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                             ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                             ->whereRaw(
//                                 '
//                                     tb_pengaduan.nama_pengaduan LIKE "%' .
//                                     $_GET['search'] .
//                                     '%" Or
//                                     tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                                     $_GET['search'] .
//                                     '%" Or
//                                     tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                                     $_GET['search'] .
//                                     '%" Or
//                                     tb_pengaduan.status_pengaduan LIKE "%' .
//                                     $_GET['search'] .
//                                     '%"
//                                 ',
//                             )
//                             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                             ->get();
//                     } else {

//                         if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
//                             $pengaduan = Pengaduan::with(['BagianKantorcabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')

//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

//                             $pengaduan = Pengaduan::with(['BagianKantorcabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         } else {
//                             $pengaduan = Pengaduan::with(['BagianKantorcabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         }

//                         // $pengaduan = DB::table('tb_pengaduan')
//                         //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                         //     ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
//                         //     ->where('tb_pengaduan.status_pengaduan', '!=', 'Checked')
//                         //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                         //     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                         //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                         //     ->paginate(12);
//                     }
//                 }
//             } elseif ($data_session_pegawai->level_pegawai == 'Kepala Unit Kerja') {

//                 if ($_GET['filter'] == 'Semua') {

//                     if (isset($_GET['search'])) {

//                         if ($data_session_pegawai->kantor_pegawai == 'Kantor Pusat') {
//                             $kepala_unit = KepalaUnit::with('BagianKantorPusat')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();

//                             $pengaduan = Pengaduan::with(['BagianKantorcabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_pusat)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->whereRaw(
//                                     '
//                                         tb_pengaduan.nama_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.status_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%"
//                                     ',
//                                 )
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->paginate(12);
//                         } elseif ($data_session_pegawai->kantor_pegawai == 'Kantor Cabang') {
//                             $kepala_unit = KepalaUnit::with('BagianKantorCabang')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();

//                             $pengaduan = Pengaduan::with(['BagianKantorCabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_cabang)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->whereRaw(
//                                     '
//                                         tb_pengaduan.nama_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.status_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%"
//                                     ',
//                                 )
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();

//                             // dd($data_session_pegawai->id_bagian_kantor_cabang);
//                         } else {
//                             $kepala_unit = KepalaUnit::with('BagianKantorWilayah')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();

//                             $pengaduan = Pengaduan::with(['BagianKantorWilayah'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_wilayah)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->whereRaw(
//                                     '
//                                         tb_pengaduan.nama_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%" Or
//                                         tb_pengaduan.status_pengaduan LIKE "%' .
//                                         $_GET['search'] .
//                                         '%"
//                                     ',
//                                 )
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         }
//                         // $pengaduan = DB::table('tb_pengaduan')
//                         //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                         //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                         //     ->whereRaw(
//                         //         '
//                         //     tb_pengaduan.nama_pengaduan LIKE "%' .
//                         //             $_GET['search'] .
//                         //             '%" Or
//                         //     tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                         //             $_GET['search'] .
//                         //             '%" Or
//                         //     tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                         //             $_GET['search'] .
//                         //             '%" Or
//                         //     tb_pengaduan.status_pengaduan LIKE "%' .
//                         //             $_GET['search'] .
//                         //             '%"
//                         // ',
//                         //     )
//                         //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                         //     ->paginate(12);
//                     } else {
//                         // $session_pegawai = Pegawai::where('id_pegawai', auth()->user()->id_pegawai)->where('delete_pegawai', 'N')->first();
//                         // dd($data_session_pegawai);
//                         if ($data_session_pegawai->kantor_pegawai == 'Kantor Pusat') {
//                             $kepala_unit = KepalaUnit::with('BagianKantorPusat')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
//                             $pengaduan = Pengaduan::with(['BagianKantorcabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_pusat)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         } elseif ($data_session_pegawai->kantor_pegawai == 'Kantor Cabang') {
//                             $kepala_unit = KepalaUnit::with('BagianKantorPusat')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();

//                             $pengaduan = Pengaduan::with(['BagianKantorcabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_cabang)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();

//                             // dd($pengaduan);
//                         } else {
//                             $kepala_unit = KepalaUnit::with('BagianKantorPusat')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
//                             $pengaduan = Pengaduan::with(['BagianKantorcabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_wilayah)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         }
//                     }
//                 } else {
//                     if (isset($_GET['search'])) {
//                         $pengaduan = DB::table('tb_pengaduan')
//                             ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                             ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                             ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                             ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                             ->whereRaw(
//                                 '
//                                     tb_pengaduan.nama_pengaduan LIKE "%' .
//                                     $_GET['search'] .
//                                     '%" Or
//                                     tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                                     $_GET['search'] .
//                                     '%" Or
//                                     tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                                     $_GET['search'] .
//                                     '%" Or
//                                     tb_pengaduan.status_pengaduan LIKE "%' .
//                                     $_GET['search'] .
//                                     '%"
//                                 ',
//                             )
//                             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                             ->get();
//                     } else {
//                         if ($data_session_pegawai->kantor_pegawai == 'Kantor Pusat') {
//                             $kepala_unit = KepalaUnit::with('BagianKantorPusat')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();

//                             $pengaduan = Pengaduan::with(['BagianKantorPusat'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_pusat)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')

//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         } elseif ($data_session_pegawai->kantor_pegawai == 'Kantor Cabang') {
//                             $kepala_unit = KepalaUnit::with('BagianKantorCabang')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();

//                             $pengaduan = Pengaduan::with(['BagianKantorCabang'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_cabang)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         } else {
//                             $kepala_unit = KepalaUnit::with('BagianKantorWilayah')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();

//                             $pengaduan = Pengaduan::with(['BagianKantorWilayah'])
//                                 ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_wilayah)
//                                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                                 ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                                 ->get();
//                         }

//                         // $pengaduan = DB::table('tb_pengaduan')
//                         //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                         //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                         //     ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
//                         //     ->where('tb_pengaduan.status_pengaduan', '!=', 'Checked')
//                         //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                         //     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                         //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                         //     ->paginate(12);
//                     }
//                 }
//             }
            
//                 if ($_GET['filter'] == 'Friend') {

//                 $session_pegawai = Pegawai::where('delete_pegawai', 'N')->where('id_pegawai', auth()->user()->id_pegawai)->get();
//                 if ($session_pegawai->count() > 0) {
//                     # code...
//                     foreach ($session_pegawai as $key => $data_session_pegawai) {


//                         if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
//                             $pengaduan = pengaduan::with('BagianKantorPusat')->where('id_bagian_kantor_pusat', $data_session_pegawai->id_bagian_kantor_pusat)->where('delete_pengaduan', 'N')->get();
//                         } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {
//                             $pengaduan = pengaduan::with('BagianKantorCabang')->where('id_bagian_kantor_cabang', $data_session_pegawai->id_bagian_kantor_cabang)->where('delete_pengaduan', 'N')->get();
//                         } elseif ($data_session_pegawai->id_bagian_kantor_wilayah != 0) {
//                             $pengaduan = pengaduan::with('BagianKantorWilayah')->where('id_bagian_kantor_wilayah', $data_session_pegawai->id_bagian_kantor_wilayahweb)->where('delete_pengaduan', 'N')->get();
//                         }
//                     }
//                 } else {
//                     # code...
//                 }
//             }

//             $status_pengaduan = array(
//                 'Pending' => 'warning',
//                 'Checked' => 'warning',
//                 'Approve' => 'info',
//                 'Read' => 'info',
//                 'Holding' => 'danger',
//                 'Moving' => 'danger',
//                 'On Progress' => 'primary',
//                 'Late' => 'danger',
//                 'Finish' => 'success',
//             );

//             $no = 1;
//             foreach ($pengaduan as $data) {

//                 // get data pegawai
//                 $pegawai = DB::table('tb_pegawai')
//                     ->where([['tb_pegawai.id_pegawai', $data->id_pegawai]])
//                     ->get();
//                 if ($pegawai->count() > 0) {
//                     foreach ($pegawai as $data_pegawai);

//                     $kantor_pegawai = '-';
//                     $bagian_pegawai = '-';

//                     if ($data_pegawai->kantor_pegawai == 'Kantor Pusat') {

//                         $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
//                             ->join('tb_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
//                             ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data_pegawai->id_bagian_kantor_pusat)
//                             ->get();
//                         if ($kantor_pusat->count() > 0) {
//                             foreach ($kantor_pusat as $data_kantor_pusat);
//                             $kantor_pegawai = $data_kantor_pusat->nama_kantor_pusat;
//                             $bagian_pegawai = $data_kantor_pusat->nama_bagian_kantor_pusat;
//                         }
//                     } else if ($data_pegawai->kantor_pegawai == 'Kantor Cabang') {

//                         $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
//                             ->join('tb_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
//                             ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data_pegawai->id_bagian_kantor_cabang)
//                             ->get();
//                         if ($kantor_cabang->count() > 0) {
//                             foreach ($kantor_cabang as $data_kantor_cabang);
//                             $kantor_pegawai = $data_kantor_cabang->nama_kantor_cabang;
//                             $bagian_pegawai = $data_kantor_cabang->nama_bagian_kantor_cabang;
//                         }
//                     } else if ($data_pegawai->kantor_pegawai == 'Kantor Wilayah') {

//                         $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
//                             ->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
//                             ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data_pegawai->id_bagian_kantor_wilayah)
//                             ->get();
//                         if ($kantor_wilayah->count() > 0) {
//                             foreach ($kantor_wilayah as $data_kantor_wilayah);
//                             $kantor_pegawai = $data_kantor_wilayah->nama_kantor_wilayah;
//                             $bagian_pegawai = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
//                         }
//                     }
//                 }
//                 // end get data pegawai

//                 // kantor bagian pengaduan
//                 $kantor_pengaduan = '-';
//                 $bagian_pengaduan = '-';

//                 if ($data->kantor_pengaduan == 'Kantor Pusat') {

//                     $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
//                         ->join('tb_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
//                         ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data->id_bagian_kantor_pusat)
//                         ->get();
//                     if ($kantor_pusat->count() > 0) {
//                         foreach ($kantor_pusat as $data_kantor_pusat);
//                         $kantor_pengaduan = $data_kantor_pusat->nama_kantor_pusat;
//                         $bagian_pengaduan = $data_kantor_pusat->nama_bagian_kantor_pusat;
//                     }
//                 } else if ($data->kantor_pengaduan == 'Kantor Cabang') {

//                     $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
//                         ->join('tb_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
//                         ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data->id_bagian_kantor_cabang)
//                         ->get();
//                     if ($kantor_cabang->count() > 0) {
//                         foreach ($kantor_cabang as $data_kantor_cabang);
//                         $kantor_pengaduan = $data_kantor_cabang->nama_kantor_cabang;
//                         $bagian_pengaduan = $data_kantor_cabang->nama_bagian_kantor_cabang;
//                     }
//                 } else if ($data->kantor_pengaduan == 'Kantor Wilayah') {

//                     $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
//                         ->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
//                         ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data->id_bagian_kantor_wilayah)
//                         ->get();
//                     if ($kantor_wilayah->count() > 0) {
//                         foreach ($kantor_wilayah as $data_kantor_wilayah);
//                         $kantor_pengaduan = $data_kantor_wilayah->nama_kantor_wilayah;
//                         $bagian_pengaduan = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
//                     }
//                 }
//                 // end kantor bagian pengaduan

//                 $delete = "delete_data(" . $data->id_pengaduan . ", '" . $data->nama_pengaduan . "')";
//                 $approve = "approve_data(" . $data->id_pengaduan . ", '" . $data->nama_pengaduan . "')";
//                 $checked = "checked_data(" . $data->id_pengaduan . ", '" . $data->nama_pengaduan . "')";
//                 $finish = "finish_data(" . $data->id_pengaduan . ", '" . $data->nama_pengaduan . "')";

//                 if ($data_session_pegawai->sebagai_pegawai == 'Petugas' && $data_session_pegawai->level_pegawai == 'Administrator') {

//                     $data->action = '-';
//                 } else if ($data_session_pegawai->sebagai_pegawai == 'Petugas' && $data_session_pegawai->level_pegawai != 'Administrator') {

//                     $data->action = '-';
//                 } else if ($data_session_pegawai->sebagai_pegawai == 'Agent') {

//                     if ($data->status_pengaduan != 'Pending' && $data->status_pengaduan != 'Finish') {
//                         $data->action = '
// 							<a href="?filter=' . $_GET['filter'] . '&alihkan=' . $data->id_pengaduan . '">
// 								<span class="badge badge-primary">
// 								  <i class="bx bx-redo"></i> Alihkan
// 								</span>
// 							</a>
// 						';
//                     } else {
//                         $data->action = '-';
//                     }
//                 } else if ($data_session_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_session_pegawai->level_pegawai != 'Staff') {

//                     if ($data_session_pegawai->level_pegawai == 'Kepala Unit Kerja') {

//                         if ($data->status_pengaduan == 'Checked') {
//                             $data->action = '
// 								<a href="javascript:;" onclick="' . $approve . '">
// 									<span class="badge badge-info">
// 									  <i class="bx bx-check-shield"></i> Approve
// 									</span>
// 								</a>
// 							';
//                         } else {
//                             $data->action = '-';
//                         }
//                     } else {

//                         if ($data->status_pengaduan == 'Pending') {
//                             $data->action = '
// 								<a href="?filter=' . $_GET['filter'] . '&lampiran=' . $data->id_pengaduan . '">
// 									<span class="badge badge-info">
// 									  <i class="bx bx-layer-plus"></i> Lampiran
// 									</span>
// 								</a>
// 								<a href="?filter=' . $_GET['filter'] . '&update=' . $data->id_pengaduan . '">
// 									<span class="badge badge-primary">
// 									  <i class="bx bx-edit"></i> Ubah
// 									</span>
// 								</a>
// 								<a href="javascript:;" onclick="' . $delete . '">
// 									<span class="badge badge-danger">
// 									  <i class="bx bx-trash"></i> Hapus
// 									</span>
// 								</a>
// 								<a href="javascript:;" onclick="' . $checked . '">
// 									<span class="badge badge-warning">
// 									  <i class="bx bx-check"></i> Checked
// 									</span>
// 								</a>
// 							';
//                         } else {
//                             if ($data->status_pengaduan == 'On Progress') {
//                                 $data->action = '
//     								<a href="javascript:;" onclick="' . $finish . '">
//     									<span class="badge badge-success">
//     									  <i class="bx bx-check-double"></i> Finish
//     									</span>
//     								</a>
//     							';
//                             } else {
//                                 $data->action = '-';
//                             }
//                         }
//                     }
//                 } else if ($data_session_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_session_pegawai->level_pegawai == 'Staff') {

//                     if ($data->status_pengaduan == 'Pending') {
//                         $data->action = '
// 							<a href="?filter=' . $_GET['filter'] . '&lampiran=' . $data->id_pengaduan . '">
// 								<span class="badge badge-info">
// 								  <i class="bx bx-layer-plus"></i> Lampiran
// 								</span>
// 							</a>
// 							<a href="?filter=' . $_GET['filter'] . '&update=' . $data->id_pengaduan . '">
// 								<span class="badge badge-primary">
// 								  <i class="bx bx-edit"></i> Ubah
// 								</span>
// 							</a>
// 							<a href="javascript:;" onclick="' . $delete . '">
// 								<span class="badge badge-danger">
// 								  <i class="bx bx-trash"></i> Hapus
// 								</span>
// 							</a>
// 						';
//                     } else {
//                         if ($data->status_pengaduan == 'On Progress') {
//                             $data->action = '
// 								<a href="javascript:;" onclick="' . $finish . '">
// 									<span class="badge badge-success">
// 									  <i class="bx bx-check-double"></i> Finish
// 									</span>
// 								</a>
// 							';
//                         } else {
//                             $data->action = '-';
//                         }
//                     }
//                 }

//                 $data->no = $no++;
//                 $data->kode_pengaduan = 'P' . date('y') . '-0000' . $data->id_pengaduan;
//                 $data->nama_pengaduan = '
// 					<a href="?filter=' . $_GET['filter'] . '&view=' . $data->id_pengaduan . '" class="text-' . $status_pengaduan[$data->status_pengaduan] . '">
// 						<b><i class="bx bxs-coupon"></i> ' . $data->nama_pengaduan . '</b>
// 					</a>
// 				';
//                 $data->dari_pengaduan = $data_pegawai->nama_pegawai . ', ' . $kantor_pegawai . ' - ' . $bagian_pegawai;
//                 $data->kepada_pengaduan = $kantor_pengaduan . ' - ' . $bagian_pengaduan;
//                 $data->keterangan_pengaduan = $data->keterangan_pengaduan;
//                 if($data->klasifikasi_pengaduan){
//                 $data->klasifikasi_pengaduan = '<b class="text-' . $status_klasifikasi[$data->klasifikasi_pengaduan] . '">' . $data->klasifikasi_pengaduan . '</b>';
//             } else {
//                      $data->klasifikasi_pengaduan = '';

//                  }
//                 $data->status_pengaduan = '
// 					<span class="badge badge-' . $status_pengaduan[$data->status_pengaduan] . '">
// 					  ' . str_replace(array('Holding', 'Hold'), array('Pengaduan SLA', 'Pengaduan SLA'), $data->status_pengaduan) . '
// 					</span>
// 				';
//                 $data->tgl_pengaduan = date('j F Y, H:i', strtotime($data->tgl_pengaduan));
//             }

//             return DataTables::of($pengaduan)->escapecolumns([])->make(true);
//         }
//     }

  

    // public function store(Request $request)
    // {
    //       $kantor = $request->kantor;
    //     $bagian_kantor_pusat = 0;
    //     $bagian_kantor_cabang = 0;
    public function datatables(Request $request)
    {

        $status_klasifikasi = array(
            'High' => 'danger',
            'Medium' => 'warning',
            'Low' => 'info',
        );


        $session_pegawai = DB::table('tb_pegawai')
            ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', auth()->user()->id_pegawai]])
            ->get();
        if ($session_pegawai->count() < 1) {
            return 0;
        } else {

            foreach ($session_pegawai as $data_session_pegawai);



            if ($data_session_pegawai->level_pegawai == 'Administrator') {
                if ($_GET['filter'] == 'Semua') {
                    if (isset($_GET['search'])) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->whereRaw(
                                '
                                    tb_pengaduan.nama_pengaduan LIKE "%' .
                                    $_GET['search'] .
                                    '%" Or
                                    tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                    $_GET['search'] .
                                    '%" Or
                                    tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                    $_GET['search'] .
                                    '%" Or
                                    tb_pengaduan.status_pengaduan LIKE "%' .
                                    $_GET['search'] .
                                    '%"
                                ',
                            )
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } else {
                        $pengaduan = DB::table('tb_pengaduan')->where('tb_pengaduan.delete_pengaduan', '=', 'N')->orderBy('tb_pengaduan.id_pengaduan', 'DESC')->get();
                    }
                } else {
                    if (isset($_GET['search'])) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                            ->whereRaw(
                                '
                                    tb_pengaduan.nama_pengaduan LIKE "%' .
                                    $_GET['search'] .
                                    '%" Or
                                    tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                    $_GET['search'] .
                                    '%" Or
                                    tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                    $_GET['search'] .
                                    '%" Or
                                    tb_pengaduan.status_pengaduan LIKE "%' .
                                    $_GET['search'] .
                                    '%"
                                ',
                            )
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } else {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    }
                }
            } elseif ($data_session_pegawai->level_pegawai == 'Staff') {

                if ($_GET['filter'] == 'Semua') {
                    if (isset($_GET['search'])) {
                        if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                            $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->whereRaw(
                                    '
                                        tb_pengaduan.nama_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.status_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%"
                                    ',
                                )
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

                            $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->whereRaw(
                                    '
                                        tb_pengaduan.nama_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.status_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%"
                                    ',
                                )
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } else {
                            $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->whereRaw(
                                    '
                                        tb_pengaduan.nama_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.status_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%"
                                    ',
                                )
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        }
                        // $pengaduan = DB::table('tb_pengaduan')
                        //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                        //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                        //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                        //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                        //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        //     ->whereRaw(
                        //         '
                        //     tb_pengaduan.nama_pengaduan LIKE "%' .
                        //             $_GET['search'] .
                        //             '%" Or
                        //     tb_pengaduan.keterangan_pengaduan LIKE "%' .
                        //             $_GET['search'] .
                        //             '%" Or
                        //     tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                        //             $_GET['search'] .
                        //             '%" Or
                        //     tb_pengaduan.status_pengaduan LIKE "%' .
                        //             $_GET['search'] .
                        //             '%"
                        // ',
                        //     )
                        //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        //     ->paginate(12);
                    } else {
                        if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                            $pengaduan = Pengaduan::with(['BagianKantorPusat'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

                            $pengaduan = Pengaduan::with(['BagianKantorCabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } else {
                            $pengaduan = Pengaduan::with(['BagianKantorWilayah'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        }
                    }
                } else {
                    if (isset($_GET['search'])) {
                        if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                            $pengaduan = Pengaduan::with(['BagianKantorPusat'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->whereRaw(
                                    '
                                        tb_pengaduan.nama_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.status_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%"
                                    ',
                                )
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

                            $pengaduan = Pengaduan::with(['BagianKantorCabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->whereRaw(
                                    '
                                        tb_pengaduan.nama_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.status_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%"
                                    ',
                                )
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } else {
                            $pengaduan = Pengaduan::with(['BagianKantorWilayah'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->whereRaw(
                                    '
                                        tb_pengaduan.nama_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.status_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%"
                                    ',
                                )
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        }
                        // $pengaduan = DB::table('tb_pengaduan')
                        //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                        //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                        //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                        //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                        //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        //     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                        //     ->whereRaw(
                        //         '
                        //     tb_pengaduan.nama_pengaduan LIKE "%' .
                        //             $_GET['search'] .
                        //             '%" Or
                        //     tb_pengaduan.keterangan_pengaduan LIKE "%' .
                        //             $_GET['search'] .
                        //             '%" Or
                        //     tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                        //             $_GET['search'] .
                        //             '%" Or
                        //     tb_pengaduan.status_pengaduan LIKE "%' .
                        //             $_GET['search'] .
                        //             '%"
                        // ',
                        //     )
                        //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        //     ->paginate(12);
                    } else {

                        if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                            $pengaduan = Pengaduan::with(['BagianKantorPusat'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

                            $pengaduan = Pengaduan::with(['BagianKantorCabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } else {
                            $pengaduan = Pengaduan::with(['BagianKantorWilayah'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        }
                        // $pengaduan = DB::table('tb_pengaduan')
                        //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                        //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                        //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                        //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                        //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        //     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                        //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        //     ->paginate(12);

                    }
                }
            } elseif ($data_session_pegawai->level_pegawai == 'Kepala Bagian Unit Kerja') {

                if ($_GET['filter'] == 'Semua') {
                    if (isset($_GET['search'])) {
                        if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                            $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->whereRaw(
                                    '
                                        tb_pengaduan.nama_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.status_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%"
                                    ',
                                )
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

                            $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->whereRaw(
                                    '
                                        tb_pengaduan.nama_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.status_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%"
                                    ',
                                )
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();

                            // dd($data_session_pegawai->id_bagian_kantor_cabang);
                        } else {
                            $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->whereRaw(
                                    '
                                        tb_pengaduan.nama_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.status_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%"
                                    ',
                                )
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        }
                        // $pengaduan = DB::table('tb_pengaduan')
                        //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                        //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                        //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                        //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                        //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        //     ->whereRaw(
                        //         '
                        //     tb_pengaduan.nama_pengaduan LIKE "%' .
                        //             $_GET['search'] .
                        //             '%" Or
                        //     tb_pengaduan.keterangan_pengaduan LIKE "%' .
                        //             $_GET['search'] .
                        //             '%" Or
                        //     tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                        //             $_GET['search'] .
                        //             '%" Or
                        //     tb_pengaduan.status_pengaduan LIKE "%' .
                        //             $_GET['search'] .
                        //             '%"
                        // ',
                        //     )
                        //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        //     ->paginate(12);
                    } else {
                        if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                            $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

                            $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();

                            // dd($data_session_pegawai->id_bagian_kantor_cabang);
                        } else {
                            $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        }
                    }
                } else {
                    if (isset($_GET['search'])) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                            ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                            ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                            ->whereRaw(
                                '
                                    tb_pengaduan.nama_pengaduan LIKE "%' .
                                    $_GET['search'] .
                                    '%" Or
                                    tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                    $_GET['search'] .
                                    '%" Or
                                    tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                    $_GET['search'] .
                                    '%" Or
                                    tb_pengaduan.status_pengaduan LIKE "%' .
                                    $_GET['search'] .
                                    '%"
                                ',
                            )
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } else {

                        if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                            $pengaduan = Pengaduan::with(['BagianKantorPusat'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {

                            $pengaduan = Pengaduan::with(['BagianKantorCabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } else {
                            $pengaduan = Pengaduan::with(['BagianKantorWilayah'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        }

                        // $pengaduan = DB::table('tb_pengaduan')
                        //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                        //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                        //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                        //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                        //     ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                        //     ->where('tb_pengaduan.status_pengaduan', '!=', 'Checked')
                        //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        //     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                        //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        //     ->paginate(12);
                    }
                }
            } elseif ($data_session_pegawai->level_pegawai == 'Kepala Unit Kerja') {

                if ($_GET['filter'] == 'Semua') {

                    if (isset($_GET['search'])) {

                        if ($data_session_pegawai->kantor_pegawai == 'Kantor Pusat') {
                            $kepala_unit = KepalaUnit::with('BagianKantorPusat')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();

                            $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_pusat)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->whereRaw(
                                    '
                                        tb_pengaduan.nama_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.status_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%"
                                    ',
                                )
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->paginate(12);
                        } elseif ($data_session_pegawai->kantor_pegawai == 'Kantor Cabang') {
                            $kepala_unit = KepalaUnit::with('BagianKantorCabang')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();

                            $pengaduan = Pengaduan::with(['BagianKantorCabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_cabang)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->whereRaw(
                                    '
                                        tb_pengaduan.nama_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.status_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%"
                                    ',
                                )
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();

                            // dd($data_session_pegawai->id_bagian_kantor_cabang);
                        } else {
                            $kepala_unit = KepalaUnit::with('BagianKantorWilayah')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();

                            $pengaduan = Pengaduan::with(['BagianKantorWilayah'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_wilayah)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->whereRaw(
                                    '
                                        tb_pengaduan.nama_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%" Or
                                        tb_pengaduan.status_pengaduan LIKE "%' .
                                        $_GET['search'] .
                                        '%"
                                    ',
                                )
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        }
                        // $pengaduan = DB::table('tb_pengaduan')
                        //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                        //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                        //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                        //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                        //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        //     ->whereRaw(
                        //         '
                        //     tb_pengaduan.nama_pengaduan LIKE "%' .
                        //             $_GET['search'] .
                        //             '%" Or
                        //     tb_pengaduan.keterangan_pengaduan LIKE "%' .
                        //             $_GET['search'] .
                        //             '%" Or
                        //     tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                        //             $_GET['search'] .
                        //             '%" Or
                        //     tb_pengaduan.status_pengaduan LIKE "%' .
                        //             $_GET['search'] .
                        //             '%"
                        // ',
                        //     )
                        //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        //     ->paginate(12);
                    } else {
                        // $session_pegawai = Pegawai::where('id_pegawai', auth()->user()->id_pegawai)->where('delete_pegawai', 'N')->first();
                        // dd($data_session_pegawai);
                        if ($data_session_pegawai->kantor_pegawai == 'Kantor Pusat') {
                            $kepala_unit = KepalaUnit::with('BagianKantorPusat')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
                            $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_pusat)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } elseif ($data_session_pegawai->kantor_pegawai == 'Kantor Cabang') {
                            $kepala_unit = KepalaUnit::with('BagianKantorPusat')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();

                            $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_cabang)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();

                            // dd($pengaduan);
                        } else {
                            $kepala_unit = KepalaUnit::with('BagianKantorPusat')->where('id_pegawai', auth()->user()->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
                            $pengaduan = Pengaduan::with(['BagianKantorcabang'])
                                ->where('tb_pengaduan.id_from_bagian', '=', $kepala_unit->id_bagian_kantor_wilayah)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        }
                    }
                } else {
                    if (isset($_GET['search'])) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                            ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                            ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                            ->whereRaw(
                                '
                                    tb_pengaduan.nama_pengaduan LIKE "%' .
                                    $_GET['search'] .
                                    '%" Or
                                    tb_pengaduan.keterangan_pengaduan LIKE "%' .
                                    $_GET['search'] .
                                    '%" Or
                                    tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
                                    $_GET['search'] .
                                    '%" Or
                                    tb_pengaduan.status_pengaduan LIKE "%' .
                                    $_GET['search'] .
                                    '%"
                                ',
                            )
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } else {
                        $kepala_unit = KepalaUnit::where('id_pegawai', $data_session_pegawai->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();

                        if ($kepala_unit->id_bagian_kantor_pusat != 0) {
                            // $kantor_pusat =KantorPusat::with('BagianKantorPusat')->where('BagianKantorPusat.id_bagian_kantor_pusat', $kepala_unit->id_bagian_kantor_pusat)->where('delete_bagian_kantor_pusat', 'N')->get();
                            $kantor_pusat = KantorPusat::with('BagianKantorPusat')
                                ->whereHas('BagianKantorPusat', function ($query) use ($kepala_unit) {
                                    $query->where('id_bagian_kantor_pusat', $kepala_unit->id_bagian_kantor_pusat)
                                        ->where('delete_bagian_kantor_pusat', 'N');
                                })
                                ->first();

                            // Kumpulkan semua ID bagian kantor wilayah
                            $bagian_ids = $kantor_pusat->BagianKantorPusat->pluck('id_bagian_kantor_pusat');

                            // Ambil semua pengaduan terkait sekaligus
                            $pengaduan = pengaduan::with('BagianKantorPusat')
                                ->whereIn('id_from_bagian', $bagian_ids)
                                ->where('delete_pengaduan', 'N')
                                // ->where(function ($query) {
                                //     $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')
                                //           ->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                                // })
                                ->where('status_pengaduan', $_GET['filter'])
                                ->get();
                        } elseif ($kepala_unit->id_bagian_kantor_cabang != 0) {
                            $kantor_cabang = KantorCabang::with('BagianKantorCabang')
                                ->whereHas('BagianKantorCabang', function ($query) use ($kepala_unit) {
                                    $query->where('id_bagian_kantor_cabang', $kepala_unit->id_bagian_kantor_cabang)
                                        ->where('delete_bagian_kantor_cabang', 'N');
                                })
                                ->first();

                            // Kumpulkan semua ID bagian kantor wilayah
                            $bagian_ids = $kantor_cabang->BagianKantorCabang->pluck('id_bagian_kantor_cabang');

                            // Ambil semua pengaduan terkait sekaligus
                            $pengaduan = pengaduan::with('BagianKantorCabang')
                                ->whereIn('id_from_bagian', $bagian_ids)
                                ->where('delete_pengaduan', 'N')
                                // ->where(function ($query) {
                                // $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')
                                //     ->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                                // })
                                ->where('status_pengaduan', $_GET['filter'])
                                ->get();
                            // dd($bagian_ids);

                            // $pengaduan = pengaduan::with('BagianKantorCabang')->where('id_bagian_kantor_cabang', $kepala_unit->id_bagian_kantor_cabang)
                            //     ->where('delete_pengaduan', 'N')->where(function ($query) {
                            //         $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                            //     })->paginate(12);
                        } elseif ($kepala_unit->id_bagian_kantor_wilayah != 0) {
                            $kantor_wilayah = KantorWilayah::with('BagianKantorWilayah')
                                ->whereHas('BagianKantorWilayah', function ($query) use ($kepala_unit) {
                                    $query->where('id_bagian_kantor_wilayah', $kepala_unit->id_bagian_kantor_wilayah)
                                        ->where('delete_bagian_kantor_wilayah', 'N');
                                })
                                ->first();

                            // Kumpulkan semua ID bagian kantor wilayah
                            $bagian_ids = $kantor_wilayah->BagianKantorWilayah->pluck('id_bagian_kantor_wilayah');

                            // Ambil semua pengaduan terkait sekaligus
                            $pengaduan = pengaduan::with('BagianKantorWilayah')
                                ->whereIn('id_from_bagian', $bagian_ids)
                                ->where('delete_pengaduan', 'N')
                                // ->where(function ($query) {
                                //     $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')
                                //         ->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                                // })
                                ->where('status_pengaduan', $_GET['filter'])
                                ->get();

                            // $pengaduan = pengaduan::with('BagianKantorWilayah')->where('id_bagian_kantor_wilayah', $kepala_unit->id_bagian_kantor_wilayahweb)
                            //     ->where('delete_pengaduan', 'N')->where(function ($query) {
                            //         $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                            //     })->paginate(12);
                        }
                        // $pengaduan = DB::table('tb_pengaduan')
                        //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                        //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                        //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                        //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                        //     ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                        //     ->where('tb_pengaduan.status_pengaduan', '!=', 'Checked')
                        //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        //     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                        //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        //     ->paginate(12);
                    }
                }
            }


            if ($_GET['filter'] == 'Friend') {

                $session_pegawai = Pegawai::where('delete_pegawai', 'N')->where('id_pegawai', auth()->user()->id_pegawai)->get();
                if ($session_pegawai->count() > 0) {
                    # code...
                    foreach ($session_pegawai as $key => $data_session_pegawai) {


                        if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                            $pengaduan = pengaduan::with('BagianKantorPusat')->where('id_bagian_kantor_pusat', $data_session_pegawai->id_bagian_kantor_pusat)->where('delete_pengaduan', 'N')->get();
                        } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {
                            $pengaduan = pengaduan::with('BagianKantorCabang')->where('id_bagian_kantor_cabang', $data_session_pegawai->id_bagian_kantor_cabang)->where('delete_pengaduan', 'N')->get();
                        } elseif ($data_session_pegawai->id_bagian_kantor_wilayah != 0) {
                            $pengaduan = pengaduan::with('BagianKantorWilayah')->where('id_bagian_kantor_wilayah', $data_session_pegawai->id_bagian_kantor_wilayahweb)->where('delete_pengaduan', 'N')->get();
                        }
                    }
                } else {
                    # code...
                }
            }

            $status_pengaduan = array(
                'Pending' => 'warning',
                'Checked' => 'warning',
                'Approve' => 'info',
                'Read' => 'info',
                'Holding' => 'danger',
                'Moving' => 'danger',
                'On Progress' => 'primary',
                'Late' => 'danger',
                'Finish' => 'success',
            );

            $no = 1;
            foreach ($pengaduan as $data) {

                // get data pegawai
                $pegawai = DB::table('tb_pegawai')
                    ->where([['tb_pegawai.id_pegawai', $data->id_pegawai]])
                    ->get();
                if ($pegawai->count() > 0) {
                    foreach ($pegawai as $data_pegawai);

                    $kantor_pegawai = '-';
                    $bagian_pegawai = '-';

                    if ($data_pegawai->kantor_pegawai == 'Kantor Pusat') {

                        $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
                            ->join('tb_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
                            ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data_pegawai->id_bagian_kantor_pusat)
                            ->get();
                        if ($kantor_pusat->count() > 0) {
                            foreach ($kantor_pusat as $data_kantor_pusat);
                            $kantor_pegawai = $data_kantor_pusat->nama_kantor_pusat;
                            $bagian_pegawai = $data_kantor_pusat->nama_bagian_kantor_pusat;
                        }
                    } else if ($data_pegawai->kantor_pegawai == 'Kantor Cabang') {

                        $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
                            ->join('tb_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
                            ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data_pegawai->id_bagian_kantor_cabang)
                            ->get();
                        if ($kantor_cabang->count() > 0) {
                            foreach ($kantor_cabang as $data_kantor_cabang);
                            $kantor_pegawai = $data_kantor_cabang->nama_kantor_cabang;
                            $bagian_pegawai = $data_kantor_cabang->nama_bagian_kantor_cabang;
                        }
                    } else if ($data_pegawai->kantor_pegawai == 'Kantor Wilayah') {

                        $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
                            ->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
                            ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data_pegawai->id_bagian_kantor_wilayah)
                            ->get();
                        if ($kantor_wilayah->count() > 0) {
                            foreach ($kantor_wilayah as $data_kantor_wilayah);
                            $kantor_pegawai = $data_kantor_wilayah->nama_kantor_wilayah;
                            $bagian_pegawai = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
                        }
                    }
                }
                // end get data pegawai

                // kantor bagian pengaduan
                $kantor_pengaduan = '-';
                $bagian_pengaduan = '-';

                if ($data->kantor_pengaduan == 'Kantor Pusat') {

                    $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
                        ->join('tb_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
                        ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data->id_bagian_kantor_pusat)
                        ->get();
                    if ($kantor_pusat->count() > 0) {
                        foreach ($kantor_pusat as $data_kantor_pusat);
                        $kantor_pengaduan = $data_kantor_pusat->nama_kantor_pusat;
                        $bagian_pengaduan = $data_kantor_pusat->nama_bagian_kantor_pusat;
                    }
                } else if ($data->kantor_pengaduan == 'Kantor Cabang') {

                    $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
                        ->join('tb_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
                        ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data->id_bagian_kantor_cabang)
                        ->get();
                    if ($kantor_cabang->count() > 0) {
                        foreach ($kantor_cabang as $data_kantor_cabang);
                        $kantor_pengaduan = $data_kantor_cabang->nama_kantor_cabang;
                        $bagian_pengaduan = $data_kantor_cabang->nama_bagian_kantor_cabang;
                    }
                } else if ($data->kantor_pengaduan == 'Kantor Wilayah') {

                    $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
                        ->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
                        ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data->id_bagian_kantor_wilayah)
                        ->get();
                    if ($kantor_wilayah->count() > 0) {
                        foreach ($kantor_wilayah as $data_kantor_wilayah);
                        $kantor_pengaduan = $data_kantor_wilayah->nama_kantor_wilayah;
                        $bagian_pengaduan = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
                    }
                }
                // end kantor bagian pengaduan

                $delete = "delete_data(" . $data->id_pengaduan . ", '" . $data->nama_pengaduan . "')";
                $approve = "approve_data(" . $data->id_pengaduan . ", '" . $data->nama_pengaduan . "')";
                $checked = "checked_data(" . $data->id_pengaduan . ", '" . $data->nama_pengaduan . "')";
                $finish = "finish_data(" . $data->id_pengaduan . ", '" . $data->nama_pengaduan . "')";

                if ($data_session_pegawai->level_pegawai == 'Administrator') {

                    $data->action = '-';
                } else if ($data_session_pegawai->level_pegawai == 'Kepala Unit Kerja' || $data_session_pegawai->level_pegawai == 'Kepala Bagian Unit Kerja') {

                    if ($data->status_pengaduan == 'Checked') {
                        $data->action = '
								<a href="javascript:;" onclick="' . $approve . '">
									<span class="badge badge-info">
									  <i class="bx bx-check-shield"></i> Approve
									</span>
								</a>
							';
                    }

                    if ($data->status_pengaduan == 'Pending') {
                        $data->action = '
								<a href="?filter=' . $_GET['filter'] . '&lampiran=' . $data->id_pengaduan . '">
									<span class="badge badge-info">
									  <i class="bx bx-layer-plus"></i> Lampiran
									</span>
								</a>
								<a href="?filter=' . $_GET['filter'] . '&update=' . $data->id_pengaduan . '">
									<span class="badge badge-primary">
									  <i class="bx bx-edit"></i> Ubah
									</span>
								</a>
								<a href="javascript:;" onclick="' . $delete . '">
									<span class="badge badge-danger">
									  <i class="bx bx-trash"></i> Hapus
									</span>
								</a>
								<a href="javascript:;" onclick="' . $checked . '">
									<span class="badge badge-warning">
									  <i class="bx bx-check"></i> Checked
									</span>
								</a>
							';
                    } else {
                        if ($data->status_pengaduan == 'On Progress') {
                            $data->action = '
    								<a href="javascript:;" onclick="' . $finish . '">
    									<span class="badge badge-success">
    									  <i class="bx bx-check-double"></i> Finish
    									</span>
    								</a>
    							';
                        } else {
                            $data->action = '-';
                        }
                    }
                } else if ($data_session_pegawai->level_pegawai == 'Staff') {

                    if ($data->status_pengaduan == 'Pending') {
                        $data->action = '
							<a href="?filter=' . $_GET['filter'] . '&lampiran=' . $data->id_pengaduan . '">
								<span class="badge badge-info">
								  <i class="bx bx-layer-plus"></i> Lampiran
								</span>
							</a>
							<a href="?filter=' . $_GET['filter'] . '&update=' . $data->id_pengaduan . '">
								<span class="badge badge-primary">
								  <i class="bx bx-edit"></i> Ubah
								</span>
							</a>
							<a href="javascript:;" onclick="' . $delete . '">
								<span class="badge badge-danger">
								  <i class="bx bx-trash"></i> Hapus
								</span>
							</a>
						';
                    } else {
                        if ($data->status_pengaduan == 'On Progress') {
                            $data->action = '
								<a href="javascript:;" onclick="' . $finish . '">
									<span class="badge badge-success">
									  <i class="bx bx-check-double"></i> Finish
									</span>
								</a>
							';
                        } else {
                            $data->action = '-';
                        }
                    }
                }

                $data->no = $no++;
                $data->kode_pengaduan = 'P' . date('y') . '-0000' . $data->id_pengaduan;
                $data->nama_pengaduan = '
					<a href="?filter=' . $_GET['filter'] . '&view=' . $data->id_pengaduan . '" class="text-' . $status_pengaduan[$data->status_pengaduan] . '">
						<b><i class="bx bxs-coupon"></i> ' . $data->nama_pengaduan . '</b>
					</a>
				';
                $data->dari_pengaduan = $data_pegawai->nama_pegawai . ', ' . $kantor_pegawai . ' - ' . $bagian_pegawai;
                $data->kepada_pengaduan = $kantor_pengaduan . ' - ' . $bagian_pengaduan;
                $data->keterangan_pengaduan = $data->keterangan_pengaduan;
                if ($data->klasifikasi_pengaduan) {
                    $data->klasifikasi_pengaduan = '<b class="text-' . $status_klasifikasi[$data->klasifikasi_pengaduan] . '">' . $data->klasifikasi_pengaduan . '</b>';
                } else {
                    $data->klasifikasi_pengaduan = '';
                }
                $data->status_pengaduan = '
					<span class="badge badge-' . $status_pengaduan[$data->status_pengaduan] . '">
					  ' . str_replace(array('Holding', 'Hold'), array('Pengaduan SLA', 'Pengaduan SLA'), $data->status_pengaduan) . '
					</span>
				';
                $data->tgl_pengaduan = date('j F Y, H:i', strtotime($data->tgl_pengaduan));
            }
            return DataTables::of($pengaduan)->escapecolumns([])->make(true);
        }
    }
    //     $bagian_kantor_wilayah = 0;
    //     $from_bagian = 0;
    //     $from_kantor = 0;
    //     if ($kantor == 'Pusat') {
    //         $bagian_kantor_pusat = $request->bagian;
    //     } else if ($kantor == 'Cabang') {
    //         $bagian_kantor_cabang = $request->bagian;
    //     } else if ($kantor == 'Wilayah') {
    //         $bagian_kantor_wilayah = $request->bagian;
    //     }
    //     $bagianPegawai = auth()->user();
    //     if ($bagianPegawai->id_bagian_kantor_pusat != 0) {
    //         $from_bagian = $bagianPegawai->id_bagian_kantor_pusat;
    //         $from_kantor = BagianKantorPusat::where('id_bagian_kantor_pusat', $from_bagian)->where('delete_bagian_kantor_pusat', 'N')->first()->id_kantor_pusat;
    //     } else if ($bagianPegawai->id_bagian_kantor_cabang != 0) {
    //         $from_bagian = $bagianPegawai->id_bagian_kantor_cabang;
    //         $from_kantor = BagianKantorCabang::where('id_bagian_kantor_cabang', $from_bagian)->where('delete_bagian_kantor_cabang', 'N')->first()->id_kantor_cabang;
    //     } else if ($bagianPegawai->id_bagian_kantor_wilayah != 0) {
    //         $from_bagian = $bagianPegawai->id_bagian_kantor_wilayah;
    //         $from_kantor = BagianKantorWilayah::where('id_bagian_kantor_wilayah', $from_bagian)->where('delete_bagian_kantor_wilayah', 'N')->first()->id_kantor_wilayah;
    //     }
    //     // return response()->json(['status' => $bagianPegawai->id_bagian_kantor_pusat]);
    //     $tanggal = Carbon::now();
    //     try {

    //         $pengaduan = new Pengaduan();
    //         $pengaduan->nama_pengaduan = $request->nama_pengaduan;
    //         $pengaduan->kantor_pengaduan = 'Kantor ' .  $kantor;
    //         $pengaduan->id_pegawai = Auth::user()->id_pegawai;
    //         $pengaduan->id_from_bagian = $from_bagian;
    //         $pengaduan->id_from_kantor = $from_kantor;
    //         $pengaduan->id_bagian_kantor_pusat = $bagian_kantor_pusat;
    //         $pengaduan->id_bagian_kantor_cabang = $bagian_kantor_cabang;
    //         $pengaduan->id_bagian_kantor_wilayah = $bagian_kantor_wilayah;
    //         $pengaduan->keterangan_pengaduan = $request->keterangan;
    //         $pengaduan->status_pengaduan = 'Pending';
    //         $pengaduan->tgl_pengaduan = $tanggal;
    //         $pengaduan->created_by = Auth::user()->nama_pegawai;
    //         $pengaduan->save();

    //         return response()->json(['status' => 'success'], 200);
    //         //code...
    //     } catch (\Exception $th) {
    //         //throw $th;
    //         return response()->json(['status' => $th->getMessage()]);
    //     }

    // }

  
     public function save(Request $request)
    {

        $startDate = now(); // Atau tanggal spesifik seperti '2024-10-03'

        $holidays = [];
        $tanggal = Carbon::parse($startDate);
        $hariKerja = 0;

        while ($hariKerja < 15) {
            $tanggal->addDay();

            // Cek apakah hari tersebut adalah hari kerja dan bukan hari libur
            if ($tanggal->isWeekday() && !in_array($tanggal->toDateString(), $holidays)) {
                $hariKerja++;
            }
        }
        $tanggal->toDateString();


        $pegawai = auth()->user()->id_pegawai;
        // dd('dfds');

        $kantors = explode(',', $request->kantor);
        $bagian = $kantors[1];
        $kantor = 'Kantor '. $kantors[0];
        $jenis_produk = explode(',', $request->jenis_produk);
        $sub_jenis_produk = $jenis_produk[1];
        $jenis_produk = $jenis_produk[0];

        $bagian_kantor_pusat = 0;
        $bagian_kantor_cabang = 0;
        $bagian_kantor_wilayah = 0;
        if ($kantor == 'Kantor Pusat') {
            $bagian_kantor_pusat = $request->bagian;
        } else if ($kantor == 'Kantor Cabang') {
            $bagian_kantor_cabang = $request->bagian;
        } else if ($kantor == 'Kantor Wilayah') {
            $bagian_kantor_wilayah = $request->bagian;
        }
        $nama = $request->nama_pengaduan;
        $keterangan = $request->keterangan;
        $klasifikasi = '';
        // $klasifikasi = $request->klasifikasi;
        $status = 'Pending';
        $tgl = date('Y-m-d H:i:s');
        $from_bagian = 0;
        $from_kantor = 0;
        // if ($kantor == 'Pusat') {
        //     $bagian_kantor_pusat = $request->sub_unit_kerja;
        // } else if ($kantor == 'Cabang') {
        //     $bagian_kantor_cabang = $request->sub_unit_kerja;
        // } else if ($kantor == 'Wilayah') {
        //     $bagian_kantor_wilayah = $request->sub_unit_kerja;
        // }
        $bagianPegawai = auth()->user();
        if ($bagianPegawai->id_bagian_kantor_pusat != 0) {
            $from_bagian = $bagianPegawai->id_bagian_kantor_pusat;
            $from_kantor = BagianKantorPusat::where('id_bagian_kantor_pusat', $from_bagian)->where('delete_bagian_kantor_pusat', 'N')->first()->id_kantor_pusat;
        } else if ($bagianPegawai->id_bagian_kantor_cabang != 0) {
            $from_bagian = $bagianPegawai->id_bagian_kantor_cabang;
            $from_kantor = BagianKantorCabang::where('id_bagian_kantor_cabang', $from_bagian)->where('delete_bagian_kantor_cabang', 'N')->first()->id_kantor_cabang;
        } else if ($bagianPegawai->id_bagian_kantor_wilayah != 0) {
            $from_bagian = $bagianPegawai->id_bagian_kantor_wilayah;
            $from_kantor = BagianKantorWilayah::where('id_bagian_kantor_wilayah', $from_bagian)->where('delete_bagian_kantor_wilayah', 'N')->first()->id_kantor_wilayah;
        }

        $values = array(
            'id_pegawai' => $pegawai,
            'kantor_pengaduan' => $kantor,
            'id_from_kantor' => $from_kantor,
            'id_from_bagian' => $from_bagian,
            'id_bagian_kantor_pusat' => $bagian_kantor_pusat,
            'id_bagian_kantor_cabang' => $bagian_kantor_cabang,
            'id_bagian_kantor_wilayah' => $bagian_kantor_wilayah,
            'nama_pengaduan' => $nama,
            'keterangan_pengaduan' => $keterangan,
             'kategori_pengaduan' => $request->kategori_pengaduan,
            'klasifikasi_pengaduan' => $klasifikasi,
            'status_pengaduan' => $status,
            'sla_pengaduan' => $tanggal,
            'jenis_produk' => $jenis_produk,
            'sub_jenis_produk' => $sub_jenis_produk,
            'tgl_pengaduan' => $tgl,
            'respon_pengaduan' => $tgl,
            'created_by' => auth()->user()->name_pegawai
        );

        DB::table('tb_pengaduan')->insert($values);

        $pengaduan = DB::table('tb_pengaduan')
            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
            ->limit(1)
            ->get();

        if ($pengaduan->count() > 0) {
            foreach ($pengaduan as $data_pengaduan) {

                $pegawai = DB::table('tb_pegawai')
                    ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai]])
                    ->get();

                if ($pegawai->count() > 0) {
                    foreach ($pegawai as $data_pegawai);

                    $get_pegawai = DB::table('tb_pegawai')
                        ->where('tb_pegawai.id_pegawai', '!=', $data_pegawai->id_pegawai)
                        ->where('tb_pegawai.delete_pegawai', '=', 'N')
                        ->where('tb_pegawai.status_pegawai', '=', 'Aktif')
                        ->where(function ($query) {
                            $query->where('tb_pegawai.level_pegawai', '!=', 'Kepala Unit Kerja');
                        })
                        ->where('tb_pegawai.id_bagian_kantor_pusat', '=', $data_pegawai->id_bagian_kantor_pusat)
                        ->where('tb_pegawai.id_bagian_kantor_cabang', '=', $data_pegawai->id_bagian_kantor_cabang)
                        ->where('tb_pegawai.id_bagian_kantor_wilayah', '=', $data_pegawai->id_bagian_kantor_wilayah)
                        ->get();

                    if ($get_pegawai->count() > 0) {
                        foreach ($get_pegawai as $data_get_pegawai) {
                            $values = array(
                                'id_pegawai' => $data_get_pegawai->id_pegawai,
                                'nama_notifikasi' => 'Pengaduan Pending',
                                'keterangan_notifikasi' => 'Pengaduan baru "' . $data_pengaduan->nama_pengaduan . '" telah diajukan oleh ' . $data_pegawai->nama_pegawai,
                                'warna_notifikasi' => 'warning',
                                'url_notifikasi' => route('pengaduan') . '?view=' . $data_pengaduan->id_pengaduan,
                                'status_notifikasi' => 'Delivery',
                                'tgl_notifikasi' => date('Y-m-d H:i:s'),
                            );
                            DB::table('tb_notifikasi')->insert($values);

                            $to_email = 'amimfaisal2@gmail.com';
                            // $to_email = $data_get_pegawai->email_pegawai;
                            $data = array(
                                'id_pengaduan' => $data_pengaduan->id_pengaduan,
                            );

                            Mail::send('pages.pengaduan.email_pending', $data, function ($message) use ($to_email) {
                                $message->to($to_email)
                                    ->subject('Pengaduan Baru (Pending)');
                                $message->from('helpdesk@cnplus.id', 'Helpdesk');
                            });
                        }
                    }
                }
            }
        }
        // return response()->json(['status' => 'success'], 200);

        return redirect()->to('pengaduan?filter=Semua');
        // return back();
    }
     public function getBagian(Request $request){
        if($request->kantor == 'Pusat')
        {
            $result = BagianKantorPusat::where('id_kantor_pusat', $request->id)->where('delete_bagian_kantor_pusat', 'N')->get();
              // Tambahkan parameter baru ke setiap item
        $result = $result->map(function ($item) {
            $item->id_bagian = $item->id_bagian_kantor_pusat;  // Tambahkan parameter baru
            $item->nama_bagian = $item->nama_bagian_kantor_pusat; // Contoh parameter tambahan (waktu sekarang)
            return $item;
        });
        }
         elseif($request->kantor == 'Cabang'){
            $result = BagianKantorCabang::where('id_kantor_cabang', $request->id)->where('delete_bagian_kantor_cabang', 'N')->get();
            $result = $result->map(function ($item) {
                $item->id_bagian = $item->id_bagian_kantor_cabang;  // Tambahkan parameter baru
                $item->nama_bagian = $item->nama_bagian_kantor_cabang; // Contoh parameter tambahan (waktu sekarang)
                return $item;
            });
    
        } else {
            $result = BagianKantorWilayah::where('id_kantor_wilayah', $request->id)->where('delete_bagian_kantor_wilayah', 'N')->get();
            $result = $result->map(function ($item) {
                $item->id_bagian = $item->id_bagian_kantor_wilayah;  // Tambahkan parameter baru
                $item->nama_bagian = $item->nama_bagian_kantor_wilayah; // Contoh parameter tambahan (waktu sekarang)
                return $item;
            });
        }
            return response()->json($result);
     }
      public function update(Request $request)
    {
        //  dd($request);
        $id = $request->update;
        $kantors = explode(',', $request->kantor);
        $bagian = $kantors[1];
        $kantor = 'Kantor ' . $kantors[0];
        $jenis_produk = explode(',', $request->jenis_produk);
        $sub_jenis_produk = $jenis_produk[1];
        $jenis_produk = $jenis_produk[0];

        $pengaduan = DB::table('tb_pengaduan')
            ->where([['tb_pengaduan.delete_pengaduan', 'N'], ['tb_pengaduan.id_pengaduan', $id], ['tb_pengaduan.status_pengaduan', 'Pending']])
            ->get();

        if ($pengaduan->count() < 1) {
            return redirect()->route('pengaduan');
        } else {
            $pegawai = auth()->user()->id_pegawai;
            // $kantor = $request->kantor;
            $bagian_kantor_pusat = 0;
            $bagian_kantor_cabang = 0;
            $bagian_kantor_wilayah = 0;
            if ($kantor == 'Kantor Pusat') {
                $bagian_kantor_pusat = $request->bagian;
            } else if ($kantor == 'Kantor Cabang') {
                $bagian_kantor_cabang = $request->bagian;
            } else if ($kantor == 'Kantor Wilayah') {
                $bagian_kantor_wilayah = $request->bagian;
            }
            $nama = $request->nama;
            $keterangan = $request->keterangan;
            $status = 'Pending';
            $tgl = date('Y-m-d H:i:s');

            $where = array(
                'id_pengaduan' => $id,
                'delete_pengaduan' => 'N',
            );
            $values = array(
                'kantor_pengaduan' => $kantor,
                'id_bagian_kantor_pusat' => $bagian_kantor_pusat,
                'id_bagian_kantor_cabang' => $bagian_kantor_cabang,
                'id_bagian_kantor_wilayah' => $bagian_kantor_wilayah,
                  'kategori_pengaduan' => $request->kategori_pengaduan,
                'nama_pengaduan' => $nama,
                'keterangan_pengaduan' => $keterangan,
                'jenis_produk' => $jenis_produk,
                'sub_jenis_produk' => $sub_jenis_produk,
            );
            DB::table('tb_pengaduan')->where($where)->update($values);

            return back()->with('alert', 'success_Berhasil diperbarui.');
        }
    }
    public function delete (Request $request){
    	$id = $request->delete;
    	$where = array(
    	  'id_pengaduan' => $id,
    	  'delete_pengaduan' => 'N',
    	);
    	$values = array(
    	  'delete_pengaduan' => 'Y',
    	);
    	DB::table('tb_pengaduan')->where($where)->update($values);
    	return back();
    }

    public function lampiran (Request $request){
    	$pengaduan = $request->pengaduan;
    	$foto = 'logos/image.png';
    	if(!empty($request->file('foto'))){
    	  $file_foto = 'file_lampiran_'.date('Ymd_His.').$request->file('foto')->getClientOriginalExtension();
    	  $request->file('foto')->move('images', $file_foto);
    	  $foto = url('images/'.$file_foto);
    	}

    	$values = array(
    	  'id_pengaduan' => $pengaduan,
    	  'file_lampiran' => $foto,
    	);
    	DB::table('tb_lampiran')->insert($values);
    	return back()->with('alert', 'success_Lampiran ditambahkan.');
    }

    public function hapus_lampiran (Request $request){
        $id = $request->delete;
        $where = array(
          'id_lampiran' => $id,
          'delete_lampiran' => 'N',
        );
        $values = array(
          'delete_lampiran' => 'Y',
        );
        DB::table('tb_lampiran')->where($where)->update($values);
        return back();
    }

    // public function approve (Request $request){
    //  $id = $request->pengaduan;

    //     $pengaduan = DB::table('tb_pengaduan')
    //     ->where([['tb_pengaduan.delete_pengaduan', 'N'], ['tb_pengaduan.status_pengaduan', 'Checked'], ['tb_pengaduan.id_pengaduan', $id]])
    //     ->get();
    //     // dd($pengaduan);
    //     if ($pengaduan->count() < 1) {
    //         return back();
    //     } else {
    //         foreach ($pengaduan as $data_pengaduan) {

    //             // get data pegawai
    //             $pegawai = DB::table('tb_pegawai')
    //                 ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai]])
    //                 ->get();
    //             if ($pegawai->count() > 0) {
    //                 foreach ($pegawai as $data_pegawai);

    //                 $get_pegawai = DB::table('tb_pegawai')
    //                     ->join('tb_kepala_unit_kerja', 'tb_kepala_unit_kerja.id_pegawai', '=', 'tb_pegawai.id_pegawai')
    //                     ->where('tb_pegawai.id_pegawai', auth()->user()->id_pegawai)
    //                     ->where('tb_pegawai.delete_pegawai', '=', 'N')
    //                     ->where('tb_pegawai.status_pegawai', '=', 'Aktif')
    //                     ->where('tb_pegawai.level_pegawai', '=', 'Kepala Unit Kerja')
    //                     ->where('tb_kepala_unit_kerja.id_bagian_kantor_pusat', '=', $data_pegawai->id_bagian_kantor_pusat)
    //                     ->where('tb_kepala_unit_kerja.id_bagian_kantor_cabang', '=', $data_pegawai->id_bagian_kantor_cabang)
    //                     ->where('tb_kepala_unit_kerja.id_bagian_kantor_wilayah', '=', $data_pegawai->id_bagian_kantor_wilayah)
    //                     ->where('tb_kepala_unit_kerja.delete_kepala_unit_kerja', '=', 'N')
    //                     ->groupBy('tb_pegawai.id_pegawai')
    //                     ->get();
    //                 if ($get_pegawai->count() > 0) {
    //                     foreach ($get_pegawai as $data_get_pegawai) {
    //                         $values = array(
    //                             'id_pengaduan' => $id,
    //                             'id_pegawai' => $data_get_pegawai->id_pegawai,
    //                             'tgl_mengetahui' => date('Y-m-d H:i:s'),
    //                         );
    //                         DB::table('tb_mengetahui')->insert($values);

    //                         $where = array(
    //                             'id_pengaduan' => $id,
    //                             'delete_pengaduan' => 'N',
    //                         );
    //                         $values = array(
    //                             'status_pengaduan' => 'Approve',
    //                             'respon_pengaduan' => date('Y-m-d H:i:s', strtotime('+1 hour')),
    //                         );
    //                         DB::table('tb_pengaduan')->where($where)->update($values);

    //                         $list_pegawai = DB::table('tb_pegawai')
    //                             ->where('tb_pegawai.delete_pegawai', '=', 'N')
    //                             ->where('tb_pegawai.status_pegawai', '=', 'Aktif')
    //                             ->where(function ($query) {
    //                                 $query->where('tb_pegawai.sebagai_pegawai', '=', 'Petugas')
    //                                     ->orWhere('tb_pegawai.sebagai_pegawai', '=', 'Agent');
    //                             })
    //                             ->where('tb_pegawai.kantor_pegawai', '=', $data_pengaduan->kantor_pengaduan)
    //                             ->where('tb_pegawai.id_bagian_kantor_pusat', '=', $data_pengaduan->id_bagian_kantor_pusat)
    //                             ->where('tb_pegawai.id_bagian_kantor_cabang', '=', $data_pengaduan->id_bagian_kantor_cabang)
    //                             ->where('tb_pegawai.id_bagian_kantor_wilayah', '=', $data_pengaduan->id_bagian_kantor_wilayah)
    //                             ->get();
    //                         if ($list_pegawai->count() > 0) {
    //                             foreach ($list_pegawai as $data_list_pegawai) {

    //                                 $values = array(
    //                                     'id_pegawai' => $data_list_pegawai->id_pegawai,
    //                                     'nama_notifikasi' => 'Pengaduan Approve',
    //                                     'keterangan_notifikasi' => 'Pengaduan "' . $data_pengaduan->nama_pengaduan . '" telah di approve oleh Mitra/Pelanggan.',
    //                                     'warna_notifikasi' => 'info',
    //                                     'url_notifikasi' => route('pengaduan') . '?view=' . $data_pengaduan->id_pengaduan,
    //                                     'status_notifikasi' => 'Delivery',
    //                                     'tgl_notifikasi' => date('Y-m-d H:i:s'),
    //                                 );
    //                                 DB::table('tb_notifikasi')->insert($values);

    //                                 $to_email = $data_list_pegawai->email_pegawai;
    //                                 $data = array(
    //                                     'id_pengaduan' => $data_pengaduan->id_pengaduan,
    //                                 );

    //                                 // Mail::send('pengaduan.email_approve', $data, function ($message) use ($to_email) {
    //                                 //     $message->to($to_email)
    //                                 //         ->subject('Pengaduan Baru (Approve)');
    //                                 //     $message->from('helpdesk@cnplus.id', 'Helpdesk');
    //                                 // });
    //                             }
    //                         }

    //                         // data kantor pegawai
    //                         $kantor_pegawai = '-';
    //                         $bagian_pegawai = '-';

    //                         if ($data_pegawai->kantor_pegawai == 'Kantor Pusat') {

    //                             $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
    //                                 ->join('tb_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
    //                                 ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data_pegawai->id_bagian_kantor_pusat)
    //                                 ->get();
    //                             if ($kantor_pusat->count() > 0) {
    //                                 foreach ($kantor_pusat as $data_kantor_pusat);
    //                                 $kantor_pegawai = $data_kantor_pusat->nama_kantor_pusat;
    //                                 $bagian_pegawai = $data_kantor_pusat->nama_bagian_kantor_pusat;
    //                             }
    //                         } else if ($data_pegawai->kantor_pegawai == 'Kantor Cabang') {

    //                             $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
    //                                 ->join('tb_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
    //                                 ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data_pegawai->id_bagian_kantor_cabang)
    //                                 ->get();
    //                             if ($kantor_cabang->count() > 0) {
    //                                 foreach ($kantor_cabang as $data_kantor_cabang);
    //                                 $kantor_pegawai = $data_kantor_cabang->nama_kantor_cabang;
    //                                 $bagian_pegawai = $data_kantor_cabang->nama_bagian_kantor_cabang;
    //                             }
    //                         } else if ($data_pegawai->kantor_pegawai == 'Kantor Wilayah') {

    //                             $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
    //                                 ->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
    //                                 ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data_pegawai->id_bagian_kantor_wilayah)
    //                                 ->get();
    //                             if ($kantor_wilayah->count() > 0) {
    //                                 foreach ($kantor_wilayah as $data_kantor_wilayah);
    //                                 $kantor_pegawai = $data_kantor_wilayah->nama_kantor_wilayah;
    //                                 $bagian_pegawai = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
    //                             }
    //                         }
    //                         // end data kantor pegawai

    //                         // kantor bagian pengaduan
    //                         $kantor_pengaduan = '-';
    //                         $bagian_pengaduan = '-';

    //                         if ($data_pengaduan->kantor_pengaduan == 'Kantor Pusat') {

    //                             $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
    //                                 ->join('tb_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
    //                                 ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data_pengaduan->id_bagian_kantor_pusat)
    //                                 ->get();
    //                             if ($kantor_pusat->count() > 0) {
    //                                 foreach ($kantor_pusat as $data_kantor_pusat);
    //                                 $kantor_pengaduan = $data_kantor_pusat->nama_kantor_pusat;
    //                                 $bagian_pengaduan = $data_kantor_pusat->nama_bagian_kantor_pusat;
    //                             }
    //                         } else if ($data_pengaduan->kantor_pengaduan == 'Kantor Cabang') {

    //                             $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
    //                                 ->join('tb_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
    //                                 ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data_pengaduan->id_bagian_kantor_cabang)
    //                                 ->get();
    //                             if ($kantor_cabang->count() > 0) {
    //                                 foreach ($kantor_cabang as $data_kantor_cabang);
    //                                 $kantor_pengaduan = $data_kantor_cabang->nama_kantor_cabang;
    //                                 $bagian_pengaduan = $data_kantor_cabang->nama_bagian_kantor_cabang;
    //                             }
    //                         } else if ($data_pengaduan->kantor_pengaduan == 'Kantor Wilayah') {

    //                             $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
    //                                 ->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
    //                                 ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data_pengaduan->id_bagian_kantor_wilayah)
    //                                 ->get();
    //                             if ($kantor_wilayah->count() > 0) {
    //                                 foreach ($kantor_wilayah as $data_kantor_wilayah);
    //                                 $kantor_pengaduan = $data_kantor_wilayah->nama_kantor_wilayah;
    //                                 $bagian_pengaduan = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
    //                             }
    //                         }
    //                         // end kantor bagian pengaduan

    //                         // create kontak
    //                         $values = array(
    //                             'created_pengaduan' => $data_pegawai->id_pegawai,
    //                             'kode_pengaduan' => 'P' . date('y') . '-0000' . $data_pengaduan->id_pengaduan,
    //                             'nama_pengaduan' => $data_pengaduan->nama_pengaduan,
    //                             'dari_kontak' => $kantor_pegawai . ' - ' . $bagian_pegawai,
    //                             'kepada_kontak' => $kantor_pengaduan . ' - ' . $bagian_pengaduan,
    //                             'role_kontak' => $data_pegawai->nama_pegawai . ' (Mitra/Pelanggan)',
    //                             'keterangan_kontak' => 'Telah melakukan pengaduan',
    //                             'tgl_kontak' => date('Y-m-d H:i:s'),
    //                         );
    //                         DB::table('tb_kontak')->insert($values);
    //                         // end create kontak

    //                         $cek_kontak = DB::table('tb_kontak')
    //                             ->where([
    //                                 ['tb_kontak.dari_kontak', $kantor_pegawai . ' - ' . $bagian_pegawai],
    //                                 ['tb_kontak.kepada_kontak', $kantor_pengaduan . ' - ' . $bagian_pengaduan],
    //                                 ['tb_kontak.kode_pengaduan', 'P' . date('y') . '-0000' . $data_pengaduan->id_pengaduan],
    //                                 ['tb_kontak.nama_pengaduan', $data_pengaduan->nama_pengaduan],
    //                                 ['tb_kontak.delete_kontak', 'N']
    //                             ])
    //                             ->where('created_pengaduan', $data_pegawai->id_pegawai)
    //                             ->orderBy('tb_kontak.id_kontak', 'DESC')
    //                             ->limit(1)
    //                             ->get();

    //                         if ($cek_kontak->count() > 0) {
    //                             foreach ($cek_kontak as $data_cek_kontak) {

    //                                 $values = array(
    //                                     'id_kontak' => $data_cek_kontak->id_kontak,
    //                                     'id_pegawai' => $data_pegawai->id_pegawai,
    //                                     'role_log_kontak' => $data_pegawai->nama_pegawai . ' (Mitra/Pelanggan)',
    //                                     'keterangan_log_kontak' => 'Telah melakukan pengaduan',
    //                                     'status_log_kontak' => 'Delivery',
    //                                     'tgl_log_kontak' => date('Y-m-d H:i:s'),
    //                                 );
    //                                 DB::table('tb_log_kontak')->insert($values);

    //                                 $list_pegawai = DB::table('tb_pegawai')
    //                                     ->where('tb_pegawai.delete_pegawai', '=', 'N')
    //                                     ->where('tb_pegawai.status_pegawai', '=', 'Aktif')
    //                                     ->where(function ($query) {
    //                                         $query->where('tb_pegawai.sebagai_pegawai', '=', 'Petugas')
    //                                             ->orWhere('tb_pegawai.sebagai_pegawai', '=', 'Agent');
    //                                     })
    //                                     ->where('tb_pegawai.kantor_pegawai', '=', $data_pengaduan->kantor_pengaduan)
    //                                     ->where('tb_pegawai.id_bagian_kantor_pusat', '=', $data_pengaduan->id_bagian_kantor_pusat)
    //                                     ->where('tb_pegawai.id_bagian_kantor_cabang', '=', $data_pengaduan->id_bagian_kantor_cabang)
    //                                     ->where('tb_pegawai.id_bagian_kantor_wilayah', '=', $data_pengaduan->id_bagian_kantor_wilayah)
    //                                     ->get();
    //                                 if ($list_pegawai->count() > 0) {
    //                                     foreach ($list_pegawai as $data_list_pegawai) {

    //                                         $values = array(
    //                                             'id_kontak' => $data_cek_kontak->id_kontak,
    //                                             'id_pegawai' => $data_list_pegawai->id_pegawai,
    //                                             'role_log_kontak' => $data_pegawai->nama_pegawai . ' (Mitra/Pelanggan)',
    //                                             'keterangan_log_kontak' => 'Telah melakukan pengaduan',
    //                                             'status_log_kontak' => 'Delivery',
    //                                             'tgl_log_kontak' => date('Y-m-d H:i:s'),
    //                                         );
    //                                         DB::table('tb_log_kontak')->insert($values);
    //                                     }
    //                                 }

    //                                 // notif chat mitra
    //                                 $list_pegawai = DB::table('tb_pegawai')
    //                                     ->join('tb_kepala_unit_kerja', 'tb_kepala_unit_kerja.id_pegawai', '=', 'tb_pegawai.id_pegawai')
    //                                     ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.sebagai_pegawai', 'Mitra/Pelanggan'], ['tb_pegawai.level_pegawai', 'Kepala Unit Kerja'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_kepala_unit_kerja.kantor_pegawai', $data_pegawai->kantor_pegawai], ['tb_kepala_unit_kerja.id_bagian_kantor_pusat', $data_pegawai->id_bagian_kantor_pusat], ['tb_kepala_unit_kerja.id_bagian_kantor_cabang', $data_pegawai->id_bagian_kantor_cabang], ['tb_kepala_unit_kerja.id_bagian_kantor_wilayah', $data_pegawai->id_bagian_kantor_wilayah]])
    //                                     ->where('tb_pegawai.id_pegawai', '!=', $data_pegawai->id_pegawai)
    //                                     ->groupBy('tb_pegawai.id_pegawai')
    //                                     ->get();

    //                                 if ($list_pegawai->count() > 0) {
    //                                     foreach ($list_pegawai as $data_list_pegawai) {

    //                                         $values = array(
    //                                             'id_kontak' => $data_cek_kontak->id_kontak,
    //                                             'id_pegawai' => $data_list_pegawai->id_pegawai,
    //                                             'role_log_kontak' => $data_pegawai->nama_pegawai . ' (Mitra/Pelanggan)',
    //                                             'keterangan_log_kontak' => 'Telah melakukan pengaduan',
    //                                             'status_log_kontak' => 'Delivery',
    //                                             'tgl_log_kontak' => date('Y-m-d H:i:s'),
    //                                         );
    //                                         DB::table('tb_log_kontak')->insert($values);
    //                                     }
    //                                 }
    //                                 // end notif chat mitra

    //                             }
    //                         }

    //                         return back()->with('alert', 'success_Pengaduan telah di approve.');
    //                     }
    //                 } else {
    //                     return back();
    //                 }
    //             } else {
    //                 return back();
    //             }
    //             // end get data pegawai
    //         }
    //     }
    // }


    public function approve(Request $request)
    {
        $id = $request->pengaduan;

        $pengaduan = DB::table('tb_pengaduan')
            ->where([['tb_pengaduan.delete_pengaduan', 'N'], ['tb_pengaduan.status_pengaduan', 'Checked'], ['tb_pengaduan.id_pengaduan', $id]])
            ->get();
        if ($pengaduan->count() < 1) {
            return back();
        } else {
            foreach ($pengaduan as $data_pengaduan) {

                // get data pegawai
                $pegawai = DB::table('tb_pegawai')
                    ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai]])
                    ->get();
                if ($pegawai->count() > 0) {
                    foreach ($pegawai as $data_pegawai);

                    $get_pegawai = DB::table('tb_pegawai')
                        ->join('tb_kepala_unit_kerja', 'tb_kepala_unit_kerja.id_pegawai', '=', 'tb_pegawai.id_pegawai')
                        ->where('tb_pegawai.id_pegawai', auth()->user()->id_pegawai)
                        ->where('tb_pegawai.delete_pegawai', '=', 'N')
                        ->where('tb_pegawai.status_pegawai', '=', 'Aktif')
                        // ->where('tb_pegawai.level_pegawai', '=', 'Kepala Bagian Unit Kerja')
                        ->where('tb_kepala_unit_kerja.id_bagian_kantor_pusat', '=', $data_pegawai->id_bagian_kantor_pusat)
                        ->where('tb_kepala_unit_kerja.id_bagian_kantor_cabang', '=', $data_pegawai->id_bagian_kantor_cabang)
                        ->where('tb_kepala_unit_kerja.id_bagian_kantor_wilayah', '=', $data_pegawai->id_bagian_kantor_wilayah)
                        ->where('tb_kepala_unit_kerja.delete_kepala_unit_kerja', '=', 'N')
                        ->groupBy('tb_pegawai.id_pegawai')
                        ->get();
                    // dd($get_pegawai);


                    if ($get_pegawai->count() > 0) {
                        foreach ($get_pegawai as $data_get_pegawai) {
                            $values = array(
                                'id_pengaduan' => $id,
                                'id_pegawai' => $data_get_pegawai->id_pegawai,
                                'tgl_mengetahui' => date('Y-m-d H:i:s'),
                            );
                            DB::table('tb_mengetahui')->insert($values);

                            $where = array(
                                'id_pengaduan' => $id,
                                'delete_pengaduan' => 'N',
                            );
                            $values = array(
                                'status_pengaduan' => 'Approve',
                                'approved_pengaduan' => $data_get_pegawai->id_pegawai,
                                'respon_pengaduan' => date('Y-m-d H:i:s', strtotime('+1 hour')),
                            );
                            DB::table('tb_pengaduan')->where($where)->update($values);
                             $values = array(
                                'id_pengaduan' => $data_pengaduan->id_pengaduan,
                                'id_pegawai' => auth()->user()->id_pegawai,
                                'created_by' => auth()->user()->nama_pegawai
                            );
                            Approved::create($values);
                            $list_pegawai = DB::table('tb_pegawai')
                                ->where('tb_pegawai.delete_pegawai', '=', 'N')
                                ->where('tb_pegawai.status_pegawai', '=', 'Aktif')
                                ->where('tb_pegawai.level_pegawai', 'Kepala Unit Kerja')
                                ->get();
                            if ($list_pegawai->count() > 0) {
                                foreach ($list_pegawai as $data_list_pegawai) {

                                    $values = array(
                                        'id_pegawai' => $data_list_pegawai->id_pegawai,
                                        'nama_notifikasi' => 'Pengaduan Approve',
                                        'keterangan_notifikasi' => 'Pengaduan "' . $data_pengaduan->nama_pengaduan . '" telah di approve',
                                        'warna_notifikasi' => 'info',
                                        'url_notifikasi' => route('pengaduan') . '?view=' . $data_pengaduan->id_pengaduan,
                                        'status_notifikasi' => 'Delivery',
                                        'tgl_notifikasi' => date('Y-m-d H:i:s'),
                                    );
                                    DB::table('tb_notifikasi')->insert($values);

                                    $to_email = 'amimfaisal2@gmail.com';
                                    $data = array(
                                        'id_pengaduan' => $data_pengaduan->id_pengaduan,
                                    );

                                    Mail::send('pages.pengaduan.email_approve', $data, function ($message) use ($to_email) {
                                        $message->to($to_email)
                                            ->subject('Pengaduan Baru (Approve)');
                                        $message->from('helpdesk@cnplus.id', 'Helpdesk');
                                    });
                                }
                            }

                            // data kantor pegawai
                            $kantor_pegawai = '-';
                            $bagian_pegawai = '-';

                            if ($data_pegawai->kantor_pegawai == 'Kantor Pusat') {

                                $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
                                    ->join('tb_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
                                    ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data_pegawai->id_bagian_kantor_pusat)
                                    ->get();
                                if ($kantor_pusat->count() > 0) {
                                    foreach ($kantor_pusat as $data_kantor_pusat);
                                    $kantor_pegawai = $data_kantor_pusat->nama_kantor_pusat;
                                    $bagian_pegawai = $data_kantor_pusat->nama_bagian_kantor_pusat;
                                }
                            } else if ($data_pegawai->kantor_pegawai == 'Kantor Cabang') {

                                $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
                                    ->join('tb_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
                                    ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data_pegawai->id_bagian_kantor_cabang)
                                    ->get();
                                if ($kantor_cabang->count() > 0) {
                                    foreach ($kantor_cabang as $data_kantor_cabang);
                                    $kantor_pegawai = $data_kantor_cabang->nama_kantor_cabang;
                                    $bagian_pegawai = $data_kantor_cabang->nama_bagian_kantor_cabang;
                                }
                            } else if ($data_pegawai->kantor_pegawai == 'Kantor Wilayah') {

                                $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
                                    ->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
                                    ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data_pegawai->id_bagian_kantor_wilayah)
                                    ->get();
                                if ($kantor_wilayah->count() > 0) {
                                    foreach ($kantor_wilayah as $data_kantor_wilayah);
                                    $kantor_pegawai = $data_kantor_wilayah->nama_kantor_wilayah;
                                    $bagian_pegawai = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
                                }
                            }
                            // end data kantor pegawai

                            // kantor bagian pengaduan
                            $kantor_pengaduan = '-';
                            $bagian_pengaduan = '-';

                            if ($data_pengaduan->kantor_pengaduan == 'Kantor Pusat') {

                                $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
                                    ->join('tb_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
                                    ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data_pengaduan->id_bagian_kantor_pusat)
                                    ->get();
                                if ($kantor_pusat->count() > 0) {
                                    foreach ($kantor_pusat as $data_kantor_pusat);
                                    $kantor_pengaduan = $data_kantor_pusat->nama_kantor_pusat;
                                    $bagian_pengaduan = $data_kantor_pusat->nama_bagian_kantor_pusat;
                                }
                            } else if ($data_pengaduan->kantor_pengaduan == 'Kantor Cabang') {

                                $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
                                    ->join('tb_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
                                    ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data_pengaduan->id_bagian_kantor_cabang)
                                    ->get();
                                if ($kantor_cabang->count() > 0) {
                                    foreach ($kantor_cabang as $data_kantor_cabang);
                                    $kantor_pengaduan = $data_kantor_cabang->nama_kantor_cabang;
                                    $bagian_pengaduan = $data_kantor_cabang->nama_bagian_kantor_cabang;
                                }
                            } else if ($data_pengaduan->kantor_pengaduan == 'Kantor Wilayah') {

                                $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
                                    ->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
                                    ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data_pengaduan->id_bagian_kantor_wilayah)
                                    ->get();
                                if ($kantor_wilayah->count() > 0) {
                                    foreach ($kantor_wilayah as $data_kantor_wilayah);
                                    $kantor_pengaduan = $data_kantor_wilayah->nama_kantor_wilayah;
                                    $bagian_pengaduan = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
                                }
                            }
                            // end kantor bagian pengaduan

                            // create kontak
                            $values = array(
                                'created_pengaduan' => $data_pegawai->id_pegawai,
                                'id_pengaduan' => $data_pengaduan->id_pengaduan,
                                'kode_pengaduan' => 'P' . date('y') . '-0000' . $data_pengaduan->id_pengaduan,
                                'nama_pengaduan' => $data_pengaduan->nama_pengaduan,
                                'dari_kontak' => $kantor_pegawai . ' - ' . $bagian_pegawai,
                                'kepada_kontak' => $kantor_pengaduan . ' - ' . $bagian_pengaduan,
                                'role_kontak' => $data_pegawai->nama_pegawai . ' ' . $data_pegawai->level_pegawai,
                                'keterangan_kontak' => 'Telah melakukan pengaduan',
                                'tgl_kontak' => date('Y-m-d H:i:s'),
                            );

                            DB::table('tb_kontak')->insert($values);
                            // end create kontak

                            $cek_kontak = DB::table('tb_kontak')
                                ->where([
                                    ['tb_kontak.dari_kontak', $kantor_pegawai . ' - ' . $bagian_pegawai],
                                    ['tb_kontak.kepada_kontak', $kantor_pengaduan . ' - ' . $bagian_pengaduan],
                                    ['tb_kontak.kode_pengaduan', 'P' . date('y') . '-0000' . $data_pengaduan->id_pengaduan],
                                    ['tb_kontak.nama_pengaduan', $data_pengaduan->nama_pengaduan],
                                    ['tb_kontak.delete_kontak', 'N']
                                ])
                                ->where('created_pengaduan', $data_pegawai->id_pegawai)
                                ->orderBy('tb_kontak.id_kontak', 'DESC')
                                ->limit(1)
                                ->get();
                            // dd($cek_kontak);
                            if ($cek_kontak->count() > 0) {
                                foreach ($cek_kontak as $data_cek_kontak) {

                                    $values = array(
                                        'id_kontak' => $data_cek_kontak->id_kontak,
                                        'id_pegawai' => $data_pegawai->id_pegawai,
                                        'role_log_kontak' => $data_pegawai->nama_pegawai . ' ' . $data_pegawai->level_pegawai,
                                        'keterangan_log_kontak' => 'Telah melakukan pengaduan',
                                        'status_log_kontak' => 'Delivery',
                                        'tgl_log_kontak' => date('Y-m-d H:i:s'),
                                    );
                                    DB::table('tb_log_kontak')->insert($values);

                                    $list_pegawai = DB::table('tb_pegawai')
                                        ->where('tb_pegawai.delete_pegawai', '=', 'N')
                                        ->where('tb_pegawai.status_pegawai', '=', 'Aktif')
                                        ->where(function ($query) {
                                            $query->where('tb_pegawai.sebagai_pegawai', '=', 'Petugas')
                                                ->orWhere('tb_pegawai.sebagai_pegawai', '=', 'Agent');
                                        })
                                        ->where('tb_pegawai.kantor_pegawai', '=', $data_pengaduan->kantor_pengaduan)
                                        ->where('tb_pegawai.id_bagian_kantor_pusat', '=', $data_pengaduan->id_bagian_kantor_pusat)
                                        ->where('tb_pegawai.id_bagian_kantor_cabang', '=', $data_pengaduan->id_bagian_kantor_cabang)
                                        ->where('tb_pegawai.id_bagian_kantor_wilayah', '=', $data_pengaduan->id_bagian_kantor_wilayah)
                                        ->get();
                                    if ($list_pegawai->count() > 0) {
                                        foreach ($list_pegawai as $data_list_pegawai) {

                                            $values = array(
                                                'id_kontak' => $data_cek_kontak->id_kontak,
                                                'id_pegawai' => $data_list_pegawai->id_pegawai,
                                                'role_log_kontak' => $data_pegawai->nama_pegawai . ' ' . $data_pegawai->level_pegawai,
                                                'keterangan_log_kontak' => 'Telah melakukan pengaduan',
                                                'status_log_kontak' => 'Delivery',
                                                'tgl_log_kontak' => date('Y-m-d H:i:s'),
                                            );
                                            DB::table('tb_log_kontak')->insert($values);
                                        }
                                    }

                                    // // notif chat mitra
                                    // $list_pegawai = DB::table('tb_pegawai')
                                    //     ->join('tb_kepala_unit_kerja', 'tb_kepala_unit_kerja.id_pegawai', '=', 'tb_pegawai.id_pegawai')
                                    //     ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.sebagai_pegawai', 'Mitra/Pelanggan'], ['tb_pegawai.level_pegawai', 'Kepala Unit Kerja'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_kepala_unit_kerja.kantor_pegawai', $data_pegawai->kantor_pegawai], ['tb_kepala_unit_kerja.id_bagian_kantor_pusat', $data_pegawai->id_bagian_kantor_pusat], ['tb_kepala_unit_kerja.id_bagian_kantor_cabang', $data_pegawai->id_bagian_kantor_cabang], ['tb_kepala_unit_kerja.id_bagian_kantor_wilayah', $data_pegawai->id_bagian_kantor_wilayah]])
                                    //     ->where('tb_pegawai.id_pegawai', '!=', $data_pegawai->id_pegawai)
                                    //     ->groupBy('tb_pegawai.id_pegawai')
                                    //     ->get();

                                    // if ($list_pegawai->count() > 0) {
                                    //     foreach ($list_pegawai as $data_list_pegawai) {

                                    //         $values = array(
                                    //             'id_kontak' => $data_cek_kontak->id_kontak,
                                    //             'id_pegawai' => $data_list_pegawai->id_pegawai,
                                    //             'role_log_kontak' => $data_pegawai->nama_pegawai . ' ' . $data_pegawai->level_pegawai,
                                    //             'keterangan_log_kontak' => 'Telah melakukan pengaduan',
                                    //             'status_log_kontak' => 'Delivery',
                                    //             'tgl_log_kontak' => date('Y-m-d H:i:s'),
                                    //         );
                                    //         DB::table('tb_log_kontak')->insert($values);
                                    //     }
                                    // }
                                    // // end notif chat mitra

                                }
                            }

                            return back()->with('alert', 'success_Pengaduan telah di approve.');
                        }
                    } else {
                        return back();
                    }
                } else {
                    return back();
                }
                // end get data pegawai
            }
        }
    }

    public function finish (Request $request){
    	$id = $request->pengaduan;

    	$pengaduan = DB::table('tb_pengaduan')
    	->where([['tb_pengaduan.delete_pengaduan','N'],['tb_pengaduan.id_pengaduan', $id]])
    	->get();
    	if($pengaduan->count() < 1){
    		return back();
    	}else{

    		foreach($pengaduan as $data_pengaduan);

    		$get_pegawai = DB::table('tb_pegawai')
    		->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai]])
    		->get();

    		foreach($get_pegawai as $data_get_pegawai);

    		$pegawai = DB::table('tb_pegawai')
    		->where('tb_pegawai.delete_pegawai','=','N')
    		->where('tb_pegawai.status_pegawai','=','Aktif')
    		->where('tb_pegawai.id_pegawai','=', $data_pengaduan->id_pegawai)
    		->get();
    		if($pegawai->count() < 1){
    			return back();
    		}else{

    			$values = array(
    			  'id_pengaduan' => $id,
    			  'id_pegawai' => Session::get('id_pegawai'),
    			  'tgl_selesai' => date('Y-m-d H:i:s'),
    			);
    			DB::table('tb_selesai')->insert($values);

    			$where = array(
    			  'id_pengaduan' => $id,
    			  'delete_pengaduan' => 'N',
    			);
    			$values = array(
    			  'status_pengaduan' => 'Finish',
    			);
    			DB::table('tb_pengaduan')->where($where)->update($values);

    // 			$list_pegawai = DB::table('tb_pegawai')
    // 			->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.sebagai_pegawai','Mitra/Pelanggan'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.kantor_pegawai', $data_get_pegawai->kantor_pegawai],['tb_pegawai.id_bagian_kantor_pusat', $data_get_pegawai->id_bagian_kantor_pusat],['tb_pegawai.id_bagian_kantor_cabang', $data_get_pegawai->id_bagian_kantor_cabang],['tb_pegawai.id_bagian_kantor_wilayah', $data_get_pegawai->id_bagian_kantor_wilayah]])
    // 			->get();

    // 			if($list_pegawai->count() > 0){
    // 				foreach($list_pegawai as $data_list_pegawai){

    // 					$values = array(
    // 					  'id_pegawai' => $data_list_pegawai->id_pegawai,
    // 					  'nama_notifikasi' => 'Pengaduan Finish',
    // 					  'keterangan_notifikasi' => 'Pengaduan "'.$data_pengaduan->nama_pengaduan.'" telah diselesaikan.',
    // 					  'warna_notifikasi' => 'success',
    // 					  'url_notifikasi' => route('pengaduan').'?view='.$data_pengaduan->id_pengaduan,
    // 					  'status_notifikasi' => 'Delivery',
    // 					  'tgl_notifikasi' => date('Y-m-d H:i:s'),
    // 					);
    // 					DB::table('tb_notifikasi')->insert($values);

    // 				// 	$to_email = $data_list_pegawai->email_pegawai;
    // 				    $to_email = 'amimfaisal2@gmail.com';
    // 					$data = array(
    // 						'id_pengaduan' => $data_pengaduan->id_pengaduan,
    // 					);

    // 					Mail::send('pages.pengaduan.email_finish', $data, function($message) use ($to_email) {
    // 						$message->to($to_email)
    // 								->subject('Pengaduan Baru (Finish)');
    // 						$message->from('helpdesk@cnplus.id','Helpdesk');
    // 					}); 

    // 				}
    // 			}
                $pegawai = DB::table('tb_pegawai')
                    ->where('tb_pegawai.delete_pegawai', '=', 'N')
                    ->where('tb_pegawai.status_pegawai', '=', 'Aktif')
                    ->where('tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai)
                    ->first();
                if ($pegawai->id_bagian_kantor_pusat != 0) {
                    $kantor_pusat = BagianKantorPusat::where('id_bagian_kantor_pusat', $pegawai->id_bagian_kantor_pusat)->where('delete_bagian_kantor_pusat', 'N')->first();
                    $list_pegawai = Pegawai::where('id_bagian_kantor_pusat', $kantor_pusat->id_bagian_kantor_pusat)->where('delete_pegawai', 'N')->get();
                } elseif ($pegawai->id_bagian_kantor_cabang != 0) {
                    $kantor_cabang = BagianKantorCabang::where('id_bagian_kantor_cabang', $pegawai->id_bagian_kantor_cabang)->where('delete_bagian_kantor_cabang', 'N')->first();
                    $list_pegawai = Pegawai::where('id_bagian_kantor_cabang', $kantor_cabang->id_bagian_kantor_cabang)->where('delete_pegawai', 'N')->get();
                } elseif ($pegawai->id_bagian_kantor_wilayah != 0) {
                    $kantor_wilayah = BagianKantorwilayah::where('id_bagian_kantor_wilayah', $pegawai->id_bagian_kantor_wilayah)->where('delete_bagian_kantor_wilayah', 'N')->first();
                    $list_pegawai = Pegawai::where('id_bagian_kantor_wilayah', $kantor_wilayah->id_bagian_kantor_wilayah)->where('delete_pegawai', 'N')->get();
                }

    			if($list_pegawai->count() > 0){
    				foreach($list_pegawai as $data_list_pegawai){

    					$values = array(
    					  'id_pegawai' => $data_list_pegawai->id_pegawai,
    					  'nama_notifikasi' => 'Pengaduan Finish',
    					  'keterangan_notifikasi' => 'Pengaduan "'.$data_pengaduan->nama_pengaduan.'" telah diselesaikan.',
    					  'warna_notifikasi' => 'success',
    					  'url_notifikasi' => route('pengaduan').'?view='.$data_pengaduan->id_pengaduan,
    					  'status_notifikasi' => 'Delivery',
    					  'tgl_notifikasi' => date('Y-m-d H:i:s'),
    					);
    					DB::table('tb_notifikasi')->insert($values);

    				// 	$to_email = $data_list_pegawai->email_pegawai;
    					$to_email = 'amimfaisal2@gmail.com';
    					$data = array(
    						'id_pengaduan' => $data_pengaduan->id_pengaduan,
    					);

    					Mail::send('pages.pengaduan.email_finish', $data, function($message) use ($to_email) {
    						$message->to($to_email)
    								->subject('Pengaduan Baru (Finish)');
    						$message->from('helpdesk@cnplus.id','Helpdesk');
    					});

    				}
    			}

    			return back()->with('alert', 'success_Pengaduan telah diselesaikan.');

    		}

    	}
    }

    // public function alihkan (Request $request){
    // 	$id = $request->pengaduan;
    // 	$kantor = $request->kantor;
    // 	$keterangan = nl2br($request->keterangan);
    // 	$bagian_kantor_pusat = 0;
    // 	$bagian_kantor_cabang = 0;
    // 	$bagian_kantor_wilayah = 0;
    // 	if($kantor == 'Kantor Pusat'){
    // 		$bagian_kantor_pusat = $request->bagian;
    // 	}else if($kantor == 'Kantor Cabang'){
    // 		$bagian_kantor_cabang = $request->bagian;
    // 	}else if($kantor == 'Kantor Wilayah'){
    // 		$bagian_kantor_wilayah = $request->bagian;
    // 	}

    // 	$pengaduan = DB::table('tb_pengaduan')
    // 	->where([['tb_pengaduan.delete_pengaduan','N'],['tb_pengaduan.id_pengaduan', $id]])
    // 	->get();
    // 	if($pengaduan->count() < 1){
    // 		return back();
    // 	}else{

    // 		foreach($pengaduan as $data_pengaduan);

    // 		$get_pegawai = $pegawai = DB::table('tb_pegawai')
    // 		->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai]])
    // 		->get();

    // 		foreach($get_pegawai as $data_get_pegawai);

    // 		$pegawai = DB::table('tb_pegawai')
    // 		->where('id_pegawai', '=', Session::get('id_pegawai'))
    // 		->where([['tb_pegawai.kantor_pegawai', $data_pengaduan->kantor_pengaduan],['tb_pegawai.id_bagian_kantor_pusat', $data_pengaduan->id_bagian_kantor_pusat],['tb_pegawai.id_bagian_kantor_cabang', $data_pengaduan->id_bagian_kantor_cabang],['tb_pegawai.id_bagian_kantor_wilayah', $data_pengaduan->id_bagian_kantor_wilayah],['tb_pegawai.sebagai_pegawai','Agent'],['tb_pegawai.level_pegawai','!=','Kepala Unit Kerja'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.delete_pegawai','N']])
    // 		->get();

    // 		if($pegawai->count() < 1){
    // 			return back();
    // 		}else{

    // 			foreach($pegawai as $data_pegawai);

    // 			$where = array(
    // 			  'id_pengaduan' => $id,
    // 			  'delete_pengaduan' => 'N',
    // 			);
    // 			$values = array(
    // 			  'kantor_pengaduan' => $kantor,
    // 			  'id_bagian_kantor_pusat' => $bagian_kantor_pusat,
    // 			  'id_bagian_kantor_cabang' => $bagian_kantor_cabang,
    // 			  'id_bagian_kantor_wilayah' => $bagian_kantor_wilayah,
    // 			  'status_pengaduan' => 'Moving',
    // 			  'respon_pengaduan' => date('Y-m-d H:i:s', strtotime('+1 hour')),
    // 			);
    // 			DB::table('tb_pengaduan')->where($where)->update($values);

    // 			$values = array(
    // 			  'id_pengaduan' => $id,
    // 			  'id_pegawai' => $data_pegawai->id_pegawai,
    // 			  'keterangan_alihkan' => $keterangan,
    // 			  'tgl_alihkan' => date('Y-m-d H:i:s'),
    // 			);
    // 			DB::table('tb_alihkan')->insert($values);

    // 			$list_pegawai = DB::table('tb_pegawai')
    // 			->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.sebagai_pegawai','Mitra/Pelanggan'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.kantor_pegawai', $data_get_pegawai->kantor_pegawai],['tb_pegawai.id_bagian_kantor_pusat', $data_get_pegawai->id_bagian_kantor_pusat],['tb_pegawai.id_bagian_kantor_cabang', $data_get_pegawai->id_bagian_kantor_cabang],['tb_pegawai.id_bagian_kantor_wilayah', $data_get_pegawai->id_bagian_kantor_wilayah]])
    // 			->get();

    // 			if($list_pegawai->count() > 0){
    // 				foreach($list_pegawai as $data_list_pegawai){

    // 					$values = array(
    // 					  'id_pegawai' => $data_list_pegawai->id_pegawai,
    // 					  'nama_notifikasi' => 'Pengaduan Moving',
    // 					  'keterangan_notifikasi' => 'Pengaduan "'.$data_pengaduan->nama_pengaduan.'" telah dialihkan.',
    // 					  'warna_notifikasi' => 'danger',
    // 					  'url_notifikasi' => route('pengaduan').'?view='.$data_pengaduan->id_pengaduan,
    // 					  'status_notifikasi' => 'Delivery',
    // 					  'tgl_notifikasi' => date('Y-m-d H:i:s'),
    // 					);
    // 					DB::table('tb_notifikasi')->insert($values);

    // 				}
    // 			}

    // 			// notif chat mitra
    // 			$list_pegawai = DB::table('tb_pegawai')
    // 			->join('tb_kepala_unit_kerja','tb_kepala_unit_kerja.id_pegawai','=','tb_pegawai.id_pegawai')
    // 			->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.sebagai_pegawai','Mitra/Pelanggan'],['tb_pegawai.level_pegawai','Kepala Unit Kerja'],['tb_pegawai.status_pegawai','Aktif'],['tb_kepala_unit_kerja.kantor_pegawai', $data_get_pegawai->kantor_pegawai],['tb_kepala_unit_kerja.id_bagian_kantor_pusat', $data_get_pegawai->id_bagian_kantor_pusat],['tb_kepala_unit_kerja.id_bagian_kantor_cabang', $data_get_pegawai->id_bagian_kantor_cabang],['tb_kepala_unit_kerja.id_bagian_kantor_wilayah', $data_get_pegawai->id_bagian_kantor_wilayah]])
    // 			->groupBy('tb_pegawai.id_pegawai')
    // 			->get();

    // 			if($list_pegawai->count() > 0){
    // 				foreach($list_pegawai as $data_list_pegawai){

    // 					$values = array(
    // 					  'id_pegawai' => $data_list_pegawai->id_pegawai,
    // 					  'nama_notifikasi' => 'Pengaduan Moving',
    // 					  'keterangan_notifikasi' => 'Pengaduan "'.$data_pengaduan->nama_pengaduan.'" telah dialihkan.',
    // 					  'warna_notifikasi' => 'danger',
    // 					  'url_notifikasi' => route('pengaduan').'?view='.$data_pengaduan->id_pengaduan,
    // 					  'status_notifikasi' => 'Delivery',
    // 					  'tgl_notifikasi' => date('Y-m-d H:i:s'),
    // 					);
    // 					DB::table('tb_notifikasi')->insert($values);

    // 				}
    // 			}
    // 			// end notif chat mitra

    // 			$latest_pengaduan = DB::table('tb_pengaduan')
    // 			->where([['tb_pengaduan.id_pengaduan', $data_pengaduan->id_pengaduan],['tb_pengaduan.delete_pengaduan','N']])
    // 			->get();

    // 			if($latest_pengaduan->count() > 0){
    // 				foreach($latest_pengaduan as $data_latest_pengaduan);

    // 				$list_pegawai = DB::table('tb_pegawai')
    // 				->where('tb_pegawai.delete_pegawai','=','N')
    // 				->where('tb_pegawai.status_pegawai','=','Aktif')
    // 				->where(function ($query) { $query->where('tb_pegawai.sebagai_pegawai','=','Petugas')
    // 				->orWhere('tb_pegawai.sebagai_pegawai','=','Agent');})
    // 				->where('tb_pegawai.kantor_pegawai','=', $data_latest_pengaduan->kantor_pengaduan)
    // 				->where('tb_pegawai.id_bagian_kantor_pusat','=', $data_latest_pengaduan->id_bagian_kantor_pusat)
    // 				->where('tb_pegawai.id_bagian_kantor_cabang','=', $data_latest_pengaduan->id_bagian_kantor_cabang)
    // 				->where('tb_pegawai.id_bagian_kantor_wilayah','=', $data_latest_pengaduan->id_bagian_kantor_wilayah)
    // 				->get();
    // 				if($list_pegawai->count() > 0){
    // 					foreach($list_pegawai as $data_list_pegawai){

    // 						$values = array(
    // 						  'id_pegawai' => $data_list_pegawai->id_pegawai,
    // 						  'nama_notifikasi' => 'Pengaduan Moving',
    // 						  'keterangan_notifikasi' => 'Pengaduan "'.$data_latest_pengaduan->nama_pengaduan.'" telah dialihkan.',
    // 						  'warna_notifikasi' => 'danger',
    // 						  'url_notifikasi' => route('pengaduan').'?view='.$data_latest_pengaduan->id_pengaduan,
    // 						  'status_notifikasi' => 'Delivery',
    // 						  'tgl_notifikasi' => date('Y-m-d H:i:s'),
    // 						);
    // 						DB::table('tb_notifikasi')->insert($values);

    // 					}
    // 				}

    // 				// data kantor pegawai
    // 				$kantor_pegawai = '-';
    // 				$bagian_pegawai = '-';

    // 				if($data_get_pegawai->kantor_pegawai == 'Kantor Pusat'){

    // 					$kantor_pusat = DB::table('tb_bagian_kantor_pusat')
    // 					->join('tb_kantor_pusat','tb_bagian_kantor_pusat.id_kantor_pusat','=','tb_kantor_pusat.id_kantor_pusat')
    // 					->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat','=', $data_get_pegawai->id_bagian_kantor_pusat)
    // 					->get();
    // 					if($kantor_pusat->count() > 0){
    // 						foreach($kantor_pusat as $data_kantor_pusat);
    // 						$kantor_pegawai = $data_kantor_pusat->nama_kantor_pusat;
    // 						$bagian_pegawai = $data_kantor_pusat->nama_bagian_kantor_pusat;
    // 					}

    // 				}else if($data_get_pegawai->kantor_pegawai == 'Kantor Cabang'){

    // 					$kantor_cabang = DB::table('tb_bagian_kantor_cabang')
    // 					->join('tb_kantor_cabang','tb_bagian_kantor_cabang.id_kantor_cabang','=','tb_kantor_cabang.id_kantor_cabang')
    // 					->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','=', $data_get_pegawai->id_bagian_kantor_cabang)
    // 					->get();
    // 					if($kantor_cabang->count() > 0){
    // 						foreach($kantor_cabang as $data_kantor_cabang);
    // 						$kantor_pegawai = $data_kantor_cabang->nama_kantor_cabang;
    // 						$bagian_pegawai = $data_kantor_cabang->nama_bagian_kantor_cabang;
    // 					}

    // 				}else if($data_get_pegawai->kantor_pegawai == 'Kantor Wilayah'){

    // 					$kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
    // 					->join('tb_kantor_wilayah','tb_bagian_kantor_wilayah.id_kantor_wilayah','=','tb_kantor_wilayah.id_kantor_wilayah')
    // 					->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','=', $data_get_pegawai->id_bagian_kantor_wilayah)
    // 					->get();
    // 					if($kantor_wilayah->count() > 0){
    // 						foreach($kantor_wilayah as $data_kantor_wilayah);
    // 						$kantor_pegawai = $data_kantor_wilayah->nama_kantor_wilayah;
    // 						$bagian_pegawai = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
    // 					}

    // 				}
    // 				// end data kantor pegawai

    // 				// kantor bagian pengaduan
    // 				$kantor_pengaduan = '-';
    // 				$bagian_pengaduan = '-';

    // 				if($data_latest_pengaduan->kantor_pengaduan == 'Kantor Pusat'){

    // 					$kantor_pusat = DB::table('tb_bagian_kantor_pusat')
    // 					->join('tb_kantor_pusat','tb_bagian_kantor_pusat.id_kantor_pusat','=','tb_kantor_pusat.id_kantor_pusat')
    // 					->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat','=', $data_latest_pengaduan->id_bagian_kantor_pusat)
    // 					->get();
    // 					if($kantor_pusat->count() > 0){
    // 						foreach($kantor_pusat as $data_kantor_pusat);
    // 						$kantor_pengaduan = $data_kantor_pusat->nama_kantor_pusat;
    // 						$bagian_pengaduan = $data_kantor_pusat->nama_bagian_kantor_pusat;
    // 					}

    // 				}else if($data_latest_pengaduan->kantor_pengaduan == 'Kantor Cabang'){

    // 					$kantor_cabang = DB::table('tb_bagian_kantor_cabang')
    // 					->join('tb_kantor_cabang','tb_bagian_kantor_cabang.id_kantor_cabang','=','tb_kantor_cabang.id_kantor_cabang')
    // 					->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','=', $data_latest_pengaduan->id_bagian_kantor_cabang)
    // 					->get();
    // 					if($kantor_cabang->count() > 0){
    // 						foreach($kantor_cabang as $data_kantor_cabang);
    // 						$kantor_pengaduan = $data_kantor_cabang->nama_kantor_cabang;
    // 						$bagian_pengaduan = $data_kantor_cabang->nama_bagian_kantor_cabang;
    // 					}

    // 				}else if($data_latest_pengaduan->kantor_pengaduan == 'Kantor Wilayah'){

    // 					$kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
    // 					->join('tb_kantor_wilayah','tb_bagian_kantor_wilayah.id_kantor_wilayah','=','tb_kantor_wilayah.id_kantor_wilayah')
    // 					->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','=', $data_latest_pengaduan->id_bagian_kantor_wilayah)
    // 					->get();
    // 					if($kantor_wilayah->count() > 0){
    // 						foreach($kantor_wilayah as $data_kantor_wilayah);
    // 						$kantor_pengaduan = $data_kantor_wilayah->nama_kantor_wilayah;
    // 						$bagian_pengaduan = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
    // 					}

    // 				}
    // 				// end kantor bagian pengaduan

    // 				// create kontak
    // 				$values = array(
    // 				  'created_pengaduan' => $data_get_pegawai->id_pegawai,
    // 				  'kode_pengaduan' => 'P'.date('y').'-0000'.$data_latest_pengaduan->id_pengaduan,
    // 				  'nama_pengaduan' => $data_latest_pengaduan->nama_pengaduan,
    // 				  'dari_kontak' => $kantor_pegawai.' - '.$bagian_pegawai,
    // 				  'kepada_kontak' => $kantor_pengaduan.' - '.$bagian_pengaduan,
    // 				  'role_kontak' => $data_pegawai->nama_pegawai.' (Agent)',
    // 				  'keterangan_kontak' => 'Pengaduan telah di alihkan',
    // 				  'tgl_kontak' => date('Y-m-d H:i:s'),
    // 				);
    // 				DB::table('tb_kontak')->insert($values);
    // 				// end create kontak

    // 				$cek_kontak = DB::table('tb_kontak')
    // 				->where([['tb_kontak.dari_kontak', $kantor_pegawai.' - '.$bagian_pegawai],
    // 					['tb_kontak.kepada_kontak', $kantor_pengaduan.' - '.$bagian_pengaduan],
    // 					['tb_kontak.kode_pengaduan', 'P'.date('y').'-0000'.$data_latest_pengaduan->id_pengaduan],
    // 					['tb_kontak.nama_pengaduan', $data_latest_pengaduan->nama_pengaduan],
    // 					['tb_kontak.delete_kontak', 'N']])
    // 				->where('created_pengaduan', $data_get_pegawai->id_pegawai)
    // 				->orderBy('tb_kontak.id_kontak','DESC')
    // 				->limit(1)
    // 				->get();

    // 				if($cek_kontak->count() > 0){
    // 					foreach($cek_kontak as $data_cek_kontak){

    // 						$values = array(
    // 						  'id_kontak' => $data_cek_kontak->id_kontak,
    // 						  'id_pegawai' => $data_get_pegawai->id_pegawai,
    // 						  'role_log_kontak' => $data_pegawai->nama_pegawai.' (Agent)',
    // 						  'keterangan_log_kontak' => 'Pengaduan telah di alihkan',
    // 						  'status_log_kontak' => 'Delivery',
    // 						  'tgl_log_kontak' => date('Y-m-d H:i:s'),
    // 						);
    // 						DB::table('tb_log_kontak')->insert($values);

    // 						$list_pegawai = DB::table('tb_pegawai')
    // 						->where('tb_pegawai.delete_pegawai','=','N')
    // 						->where('tb_pegawai.status_pegawai','=','Aktif')
    // 						->where(function ($query) { $query->where('tb_pegawai.sebagai_pegawai','=','Petugas')
    // 						->orWhere('tb_pegawai.sebagai_pegawai','=','Agent');})
    // 						->where('tb_pegawai.kantor_pegawai','=', $data_latest_pengaduan->kantor_pengaduan)
    // 						->where('tb_pegawai.id_bagian_kantor_pusat','=', $data_latest_pengaduan->id_bagian_kantor_pusat)
    // 						->where('tb_pegawai.id_bagian_kantor_cabang','=', $data_latest_pengaduan->id_bagian_kantor_cabang)
    // 						->where('tb_pegawai.id_bagian_kantor_wilayah','=', $data_latest_pengaduan->id_bagian_kantor_wilayah)
    // 						->get();
    // 						if($list_pegawai->count() > 0){
    // 							foreach($list_pegawai as $data_list_pegawai){

    // 								$values = array(
    // 								  'id_kontak' => $data_cek_kontak->id_kontak,
    // 								  'id_pegawai' => $data_list_pegawai->id_pegawai,
    // 								  'role_log_kontak' => $data_pegawai->nama_pegawai.' (Agent)',
    // 							  	  'keterangan_log_kontak' => 'Pengaduan telah di alihkan',
    // 								  'status_log_kontak' => 'Delivery',
    // 								  'tgl_log_kontak' => date('Y-m-d H:i:s'),
    // 								);
    // 								DB::table('tb_log_kontak')->insert($values);

    // 							}
    // 						}

    // 						// notif chat mitra
    // 						$list_pegawai = DB::table('tb_pegawai')
    // 						->join('tb_kepala_unit_kerja','tb_kepala_unit_kerja.id_pegawai','=','tb_pegawai.id_pegawai')
    // 						->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.sebagai_pegawai','Mitra/Pelanggan'],['tb_pegawai.level_pegawai','Kepala Unit Kerja'],['tb_pegawai.status_pegawai','Aktif'],['tb_kepala_unit_kerja.kantor_pegawai', $data_get_pegawai->kantor_pegawai],['tb_kepala_unit_kerja.id_bagian_kantor_pusat', $data_get_pegawai->id_bagian_kantor_pusat],['tb_kepala_unit_kerja.id_bagian_kantor_cabang', $data_get_pegawai->id_bagian_kantor_cabang],['tb_kepala_unit_kerja.id_bagian_kantor_wilayah', $data_get_pegawai->id_bagian_kantor_wilayah]])
    // 						->where('tb_pegawai.id_pegawai', '!=', $data_get_pegawai->id_pegawai)
    // 						->groupBy('tb_pegawai.id_pegawai')
    // 						->get();

    // 						if($list_pegawai->count() > 0){
    // 							foreach($list_pegawai as $data_list_pegawai){

    // 								$values = array(
    // 								  'id_kontak' => $data_cek_kontak->id_kontak,
    // 								  'id_pegawai' => $data_list_pegawai->id_pegawai,
    // 								  'role_log_kontak' => $data_pegawai->nama_pegawai.' (Agent)',
    // 							  	  'keterangan_log_kontak' => 'Pengaduan telah di alihkan',
    // 								  'status_log_kontak' => 'Delivery',
    // 								  'tgl_log_kontak' => date('Y-m-d H:i:s'),
    // 								);
    // 								DB::table('tb_log_kontak')->insert($values);

    // 							}
    // 						}
    // 						// end notif chat mitra

    // 					}
    // 				}
    // 			}


    // 			return redirect()->to('pengaduan?filter='.$_GET['filter'])->with('alert', 'success_Berhasil dialihkan.');

    // 		}

    // 	}
    // }
       public function alihkan(Request $request)
    {
        // dd($request);
        $id = $request->pengaduan;
        // $kantor = $request->kantor;
        $kantors = explode(',', $request->kantor);
        $bagian = $kantors[1];
        $kantor = 'Kantor ' . $kantors[0];
        $keterangan = nl2br($request->keterangan);
        $bagian_kantor_pusat = 0;
        $bagian_kantor_cabang = 0;
        $bagian_kantor_wilayah = 0;
        if ($kantor == 'Kantor Pusat') {
            $bagian_kantor_pusat = $request->bagian;
        } else if ($kantor == 'Kantor Cabang') {
            $bagian_kantor_cabang = $request->bagian;
        } else if ($kantor == 'Kantor Wilayah') {
            $bagian_kantor_wilayah = $request->bagian;
        }


        $pengaduan = DB::table('tb_pengaduan')
            ->where([['tb_pengaduan.delete_pengaduan', 'N'], ['tb_pengaduan.id_pengaduan', $id]])
            ->get();
        if ($pengaduan->count() < 1) {
            return back();
        } else {

            foreach ($pengaduan as $data_pengaduan);

            $get_pegawai = $pegawai = DB::table('tb_pegawai')
                ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai]])
                ->get();

            foreach ($get_pegawai as $data_get_pegawai);

            $pegawai = DB::table('tb_pegawai')
                ->where('id_pegawai', '=', auth()->user()->id_pegawai)
                ->where([['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.delete_pegawai', 'N']])
                ->get();

            if ($pegawai->count() < 1) {
                return back();
            } else {

                foreach ($pegawai as $data_pegawai);

                $where = array(
                    'id_pengaduan' => $id,
                    'delete_pengaduan' => 'N',
                );
                $values = array(
                    'kantor_pengaduan' => $kantor,
                    'id_bagian_kantor_pusat' => $bagian_kantor_pusat,
                    'id_bagian_kantor_cabang' => $bagian_kantor_cabang,
                    'id_bagian_kantor_wilayah' => $bagian_kantor_wilayah,
                    'status_pengaduan' => 'Moving',
                    'respon_pengaduan' => date('Y-m-d H:i:s', strtotime('+1 hour')),
                );
                DB::table('tb_pengaduan')->where($where)->update($values);

                $values = array(
                    'id_pengaduan' => $id,
                    'id_pegawai' => $data_pegawai->id_pegawai,
                    'keterangan_alihkan' => $keterangan,
                    'tgl_alihkan' => date('Y-m-d H:i:s'),
                );
                DB::table('tb_alihkan')->insert($values);

                // $list_pegawai = DB::table('tb_pegawai')
                //     ->where([['tb_pegawai.delete_pegawai', 'N'],['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.kantor_pegawai', $data_get_pegawai->kantor_pegawai], ['tb_pegawai.id_bagian_kantor_pusat', $data_get_pegawai->id_bagian_kantor_pusat], ['tb_pegawai.id_bagian_kantor_cabang', $data_get_pegawai->id_bagian_kantor_cabang], ['tb_pegawai.id_bagian_kantor_wilayah', $data_get_pegawai->id_bagian_kantor_wilayah]])
                //     ->get();
                $pegawai = DB::table('tb_pegawai')
                    ->where('tb_pegawai.delete_pegawai', '=', 'N')
                    ->where('tb_pegawai.status_pegawai', '=', 'Aktif')
                    ->where('tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai)
                    ->first();
                if ($pegawai->id_bagian_kantor_pusat != 0) {
                    $kantor_pusat = BagianKantorPusat::where('id_bagian_kantor_pusat', $pegawai->id_bagian_kantor_pusat)->where('delete_bagian_kantor_pusat', 'N')->first();
                    $list_pegawai = Pegawai::where('id_bagian_kantor_pusat', $kantor_pusat->id_bagian_kantor_pusat)->where('delete_pegawai', 'N')->get();
                } elseif ($pegawai->id_bagian_kantor_cabang != 0) {
                    $kantor_cabang = BagianKantorCabang::where('id_bagian_kantor_cabang', $pegawai->id_bagian_kantor_cabang)->where('delete_bagian_kantor_cabang', 'N')->first();
                    $list_pegawai = Pegawai::where('id_bagian_kantor_cabang', $kantor_cabang->id_bagian_kantor_cabang)->where('delete_pegawai', 'N')->get();
                } elseif ($pegawai->id_bagian_kantor_wilayah != 0) {
                    $kantor_wilayah = BagianKantorwilayah::where('id_bagian_kantor_wilayah', $pegawai->id_bagian_kantor_wilayah)->where('delete_bagian_kantor_wilayah', 'N')->first();
                    $list_pegawai = Pegawai::where('id_bagian_kantor_wilayah', $kantor_wilayah->id_bagian_kantor_wilayah)->where('delete_pegawai', 'N')->get();
                }
                // dd($list_pegawai);
                if ($list_pegawai->count() > 0) {
                    foreach ($list_pegawai as $data_list_pegawai) {

                        $values = array(
                            'id_pegawai' => $data_list_pegawai->id_pegawai,
                            'nama_notifikasi' => 'Pengaduan Moving',
                            'keterangan_notifikasi' => 'Pengaduan "' . $data_pengaduan->nama_pengaduan . '" telah dialihkan.',
                            'warna_notifikasi' => 'danger',
                            'url_notifikasi' => route('pengaduan') . '?view=' . $data_pengaduan->id_pengaduan,
                            'status_notifikasi' => 'Delivery',
                            'tgl_notifikasi' => date('Y-m-d H:i:s'),
                        );
                        DB::table('tb_notifikasi')->insert($values);
                    }
                }

                // // notif chat mitra
                // $list_pegawai = DB::table('tb_pegawai')
                //     ->join('tb_kepala_unit_kerja', 'tb_kepala_unit_kerja.id_pegawai', '=', 'tb_pegawai.id_pegawai')
                //     ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.sebagai_pegawai', 'Mitra/Pelanggan'], ['tb_pegawai.level_pegawai', 'Kepala Unit Kerja'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_kepala_unit_kerja.kantor_pegawai', $data_get_pegawai->kantor_pegawai], ['tb_kepala_unit_kerja.id_bagian_kantor_pusat', $data_get_pegawai->id_bagian_kantor_pusat], ['tb_kepala_unit_kerja.id_bagian_kantor_cabang', $data_get_pegawai->id_bagian_kantor_cabang], ['tb_kepala_unit_kerja.id_bagian_kantor_wilayah', $data_get_pegawai->id_bagian_kantor_wilayah]])
                //     ->groupBy('tb_pegawai.id_pegawai')
                //     ->get();

                // if ($list_pegawai->count() > 0) {
                //     foreach ($list_pegawai as $data_list_pegawai) {

                //         $values = array(
                //             'id_pegawai' => $data_list_pegawai->id_pegawai,
                //             'nama_notifikasi' => 'Pengaduan Moving',
                //             'keterangan_notifikasi' => 'Pengaduan "' . $data_pengaduan->nama_pengaduan . '" telah dialihkan.',
                //             'warna_notifikasi' => 'danger',
                //             'url_notifikasi' => route('pengaduan') . '?view=' . $data_pengaduan->id_pengaduan,
                //             'status_notifikasi' => 'Delivery',
                //             'tgl_notifikasi' => date('Y-m-d H:i:s'),
                //         );
                //         DB::table('tb_notifikasi')->insert($values);
                //     }
                // }
                // // end notif chat mitra

                $latest_pengaduan = DB::table('tb_pengaduan')
                    ->where([['tb_pengaduan.id_pengaduan', $data_pengaduan->id_pengaduan], ['tb_pengaduan.delete_pengaduan', 'N']])
                    ->get();

                    foreach ($latest_pengaduan as $data_latest_pengaduan);
             

                // data kantor pegawai
                $kantor_pegawai = '-';
                $bagian_pegawai = '-';

                if ($data_get_pegawai->kantor_pegawai == 'Kantor Pusat') {

                    $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
                        ->join('tb_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
                        ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data_get_pegawai->id_bagian_kantor_pusat)
                        ->get();
                    if ($kantor_pusat->count() > 0) {
                        foreach ($kantor_pusat as $data_kantor_pusat);
                        $kantor_pegawai = $data_kantor_pusat->nama_kantor_pusat;
                        $bagian_pegawai = $data_kantor_pusat->nama_bagian_kantor_pusat;
                    }
                } else if ($data_get_pegawai->kantor_pegawai == 'Kantor Cabang') {

                    $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
                        ->join('tb_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
                        ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data_get_pegawai->id_bagian_kantor_cabang)
                        ->get();
                    if ($kantor_cabang->count() > 0) {
                        foreach ($kantor_cabang as $data_kantor_cabang);
                        $kantor_pegawai = $data_kantor_cabang->nama_kantor_cabang;
                        $bagian_pegawai = $data_kantor_cabang->nama_bagian_kantor_cabang;
                    }
                } else if ($data_get_pegawai->kantor_pegawai == 'Kantor Wilayah') {

                    $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
                        ->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
                        ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data_get_pegawai->id_bagian_kantor_wilayah)
                        ->get();
                    if ($kantor_wilayah->count() > 0) {
                        foreach ($kantor_wilayah as $data_kantor_wilayah);
                        $kantor_pegawai = $data_kantor_wilayah->nama_kantor_wilayah;
                        $bagian_pegawai = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
                    }
                }
                // end data kantor pegawai

                // kantor bagian pengaduan
                $kantor_pengaduan = '-';
                $bagian_pengaduan = '-';

                if ($data_latest_pengaduan->kantor_pengaduan == 'Kantor Pusat') {

                    $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
                        ->join('tb_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
                        ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data_latest_pengaduan->id_bagian_kantor_pusat)
                        ->get();
                    if ($kantor_pusat->count() > 0) {
                        foreach ($kantor_pusat as $data_kantor_pusat);
                        $kantor_pengaduan = $data_kantor_pusat->nama_kantor_pusat;
                        $bagian_pengaduan = $data_kantor_pusat->nama_bagian_kantor_pusat;
                    }
                } else if ($data_latest_pengaduan->kantor_pengaduan == 'Kantor Cabang') {

                    $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
                        ->join('tb_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
                        ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data_latest_pengaduan->id_bagian_kantor_cabang)
                        ->get();
                    if ($kantor_cabang->count() > 0) {
                        foreach ($kantor_cabang as $data_kantor_cabang);
                        $kantor_pengaduan = $data_kantor_cabang->nama_kantor_cabang;
                        $bagian_pengaduan = $data_kantor_cabang->nama_bagian_kantor_cabang;
                    }
                } else if ($data_latest_pengaduan->kantor_pengaduan == 'Kantor Wilayah') {

                    $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
                        ->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
                        ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data_latest_pengaduan->id_bagian_kantor_wilayah)
                        ->get();
                    if ($kantor_wilayah->count() > 0) {
                        foreach ($kantor_wilayah as $data_kantor_wilayah);
                        $kantor_pengaduan = $data_kantor_wilayah->nama_kantor_wilayah;
                        $bagian_pengaduan = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
                    }
                }
                // end kantor bagian pengaduan

                // create kontak
                $values = array(
                    'created_pengaduan' => $data_get_pegawai->id_pegawai,
                    'kode_pengaduan' => 'P' . date('y') . '-0000' . $data_latest_pengaduan->id_pengaduan,
                    'nama_pengaduan' => $data_latest_pengaduan->nama_pengaduan,
                    'dari_kontak' => $kantor_pegawai . ' - ' . $bagian_pegawai,
                    'kepada_kontak' => $kantor_pengaduan . ' - ' . $bagian_pengaduan,
                    'role_kontak' => $data_pegawai->nama_pegawai . ' (Agent)',
                    'keterangan_kontak' => 'Pengaduan telah di alihkan',
                    'tgl_kontak' => date('Y-m-d H:i:s'),
                );
                DB::table('tb_kontak')->insert($values);
                // end create kontak

                $cek_kontak = DB::table('tb_kontak')
                    ->where([
                        ['tb_kontak.dari_kontak', $kantor_pegawai . ' - ' . $bagian_pegawai],
                        ['tb_kontak.kepada_kontak', $kantor_pengaduan . ' - ' . $bagian_pengaduan],
                        ['tb_kontak.kode_pengaduan', 'P' . date('y') . '-0000' . $data_latest_pengaduan->id_pengaduan],
                        ['tb_kontak.nama_pengaduan', $data_latest_pengaduan->nama_pengaduan],
                        ['tb_kontak.delete_kontak', 'N']
                    ])
                    ->where('created_pengaduan', $data_get_pegawai->id_pegawai)
                    ->orderBy('tb_kontak.id_kontak', 'DESC')
                    ->limit(1)
                    ->get();

                if ($cek_kontak->count() > 0) {
                    foreach ($cek_kontak as $data_cek_kontak) {

                        $values = array(
                            'id_kontak' => $data_cek_kontak->id_kontak,
                            'id_pegawai' => $data_get_pegawai->id_pegawai,
                            'role_log_kontak' => $data_pegawai->nama_pegawai . ' (Agent)',
                            'keterangan_log_kontak' => 'Pengaduan telah di alihkan',
                            'status_log_kontak' => 'Delivery',
                            'tgl_log_kontak' => date('Y-m-d H:i:s'),
                        );
                        DB::table('tb_log_kontak')->insert($values);

                        $list_pegawai = DB::table('tb_pegawai')
                            ->where('tb_pegawai.delete_pegawai', '=', 'N')
                            ->where('tb_pegawai.status_pegawai', '=', 'Aktif')
                            ->where(function ($query) {
                                $query->where('tb_pegawai.sebagai_pegawai', '=', 'Petugas')
                                    ->orWhere('tb_pegawai.sebagai_pegawai', '=', 'Agent');
                            })
                            ->where('tb_pegawai.kantor_pegawai', '=', $data_latest_pengaduan->kantor_pengaduan)
                            ->where('tb_pegawai.id_bagian_kantor_pusat', '=', $data_latest_pengaduan->id_bagian_kantor_pusat)
                            ->where('tb_pegawai.id_bagian_kantor_cabang', '=', $data_latest_pengaduan->id_bagian_kantor_cabang)
                            ->where('tb_pegawai.id_bagian_kantor_wilayah', '=', $data_latest_pengaduan->id_bagian_kantor_wilayah)
                            ->get();
                        if ($list_pegawai->count() > 0) {
                            foreach ($list_pegawai as $data_list_pegawai) {

                                $values = array(
                                    'id_kontak' => $data_cek_kontak->id_kontak,
                                    'id_pegawai' => $data_list_pegawai->id_pegawai,
                                    'role_log_kontak' => $data_pegawai->nama_pegawai . ' (Agent)',
                                    'keterangan_log_kontak' => 'Pengaduan telah di alihkan',
                                    'status_log_kontak' => 'Delivery',
                                    'tgl_log_kontak' => date('Y-m-d H:i:s'),
                                );
                                DB::table('tb_log_kontak')->insert($values);
                            }
                        }

                        // notif chat mitra
                        $list_pegawai = DB::table('tb_pegawai')
                            ->join('tb_kepala_unit_kerja', 'tb_kepala_unit_kerja.id_pegawai', '=', 'tb_pegawai.id_pegawai')
                            ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.sebagai_pegawai', 'Mitra/Pelanggan'], ['tb_pegawai.level_pegawai', 'Kepala Unit Kerja'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_kepala_unit_kerja.kantor_pegawai', $data_get_pegawai->kantor_pegawai], ['tb_kepala_unit_kerja.id_bagian_kantor_pusat', $data_get_pegawai->id_bagian_kantor_pusat], ['tb_kepala_unit_kerja.id_bagian_kantor_cabang', $data_get_pegawai->id_bagian_kantor_cabang], ['tb_kepala_unit_kerja.id_bagian_kantor_wilayah', $data_get_pegawai->id_bagian_kantor_wilayah]])
                            ->where('tb_pegawai.id_pegawai', '!=', $data_get_pegawai->id_pegawai)
                            ->groupBy('tb_pegawai.id_pegawai')
                            ->get();

                        if ($list_pegawai->count() > 0) {
                            foreach ($list_pegawai as $data_list_pegawai) {

                                $values = array(
                                    'id_kontak' => $data_cek_kontak->id_kontak,
                                    'id_pegawai' => $data_list_pegawai->id_pegawai,
                                    'role_log_kontak' => $data_pegawai->nama_pegawai . ' (Agent)',
                                    'keterangan_log_kontak' => 'Pengaduan telah di alihkan',
                                    'status_log_kontak' => 'Delivery',
                                    'tgl_log_kontak' => date('Y-m-d H:i:s'),
                                );
                                DB::table('tb_log_kontak')->insert($values);
                            }
                        }
                        // end notif chat mitra

                    }
                }
                return redirect()->to('pengaduan?filter=' . $_GET['filter'])->with('alert', 'success_Berhasil dialihkan.');
            }
        }
    }

    // public function jawaban (Request $request){
    //     $email_smtp = null;
    //     // $email_smtp = new SMTP_Send();

    // 	$id = $request->pengaduan;
    // 	$id_pegawai = Session::get('id_pegawai');
    // 	$keterangan = nl2br($request->keterangan);
    // 	$foto = 'logos/image.png';
    // 	if(!empty($request->file('foto'))){
    // 	  $file_foto = 'foto_jawaban_'.date('Ymd_His.').$request->file('foto')->getClientOriginalExtension();
    // 	  $request->file('foto')->move('images', $file_foto);
    // 	  $foto = url('images/'.$file_foto);
    // 	}
    // 	$sla = $request->sla;
    // 	$durasi_sla = date('Y-m-d H:i:s', strtotime('+'.$request->durasi_sla.' day'));
    // 	$alasan_sla = nl2br($request->alasan_sla);
    // 	$tgl = date('Y-m-d H:i:s');

    // 	$pengaduan = DB::table('tb_pengaduan')
    // 	->where([['tb_pengaduan.delete_pengaduan','N'],['tb_pengaduan.id_pengaduan', $id]])
    // 	->get();

    // 	if($pengaduan->count() < 1){
    // 		return back();
    // 	}else{

    // 		foreach($pengaduan as $data_pengaduan);

    // 		$pegawai = DB::table('tb_pegawai')
    // 		->where([['tb_pegawai.id_pegawai', $id_pegawai],['tb_pegawai.kantor_pegawai', $data_pengaduan->kantor_pengaduan],['tb_pegawai.id_bagian_kantor_pusat', $data_pengaduan->id_bagian_kantor_pusat],['tb_pegawai.id_bagian_kantor_cabang', $data_pengaduan->id_bagian_kantor_cabang],['tb_pegawai.id_bagian_kantor_wilayah', $data_pengaduan->id_bagian_kantor_wilayah],['tb_pegawai.delete_pegawai','N'],['tb_pegawai.sebagai_pegawai','Agent'],['tb_pegawai.status_pegawai','Aktif']])
    // 		->where('tb_pegawai.level_pegawai','!=','Kepala Unit Kerja')
    // 		->get();
    // 		if($pegawai->count() < 1){
    // 			return back();
    // 		}else{

    // 			$values = array(
    // 			  'id_pengaduan' => $id,
    // 			  'id_pegawai' => $id_pegawai,
    // 			  'keterangan_jawaban' => $keterangan,
    // 			  'foto_jawaban' => $foto,
    // 			  'sla_jawaban' => $sla,
    // 			  'durasi_sla_jawaban' => $durasi_sla,
    // 			  'alasan_sla_jawaban' => $alasan_sla,
    // 			  'tgl_jawaban' => $tgl,
    // 			);
    // 			DB::table('tb_jawaban')->insert($values);

    // 			if($sla == 'Ya'){

    // 				$where = array(
    // 				  'id_pengaduan' => $id,
    // 				  'delete_pengaduan' => 'N',
    // 				);
    // 				$values = array(
    // 				  'status_pengaduan' => 'Holding',
    // 				);
    // 				DB::table('tb_pengaduan')->where($where)->update($values);

    // 				$list_pegawai = DB::table('tb_pegawai')
    // 				->where('tb_pegawai.delete_pegawai','=','N')
    // 				->where('tb_pegawai.status_pegawai','=','Aktif')
    // 				->where(function ($query) { $query->where('tb_pegawai.sebagai_pegawai','=','Petugas')
    // 				->orWhere('tb_pegawai.sebagai_pegawai','=','Agent');})
    // 				->where('tb_pegawai.kantor_pegawai','=', $data_pengaduan->kantor_pengaduan)
    // 				->where('tb_pegawai.id_bagian_kantor_pusat','=', $data_pengaduan->id_bagian_kantor_pusat)
    // 				->where('tb_pegawai.id_bagian_kantor_cabang','=', $data_pengaduan->id_bagian_kantor_cabang)
    // 				->where('tb_pegawai.id_bagian_kantor_wilayah','=', $data_pengaduan->id_bagian_kantor_wilayah)
    // 				->get();
    // 				if($list_pegawai->count() > 0){
    // 					foreach($list_pegawai as $data_list_pegawai){

    // 						$values = array(
    // 						  'id_pegawai' => $data_list_pegawai->id_pegawai,
    // 						  'nama_notifikasi' => 'Pengaduan Holding',
    // 						  'keterangan_notifikasi' => 'Pengaduan "'.$data_pengaduan->nama_pengaduan.'" tertunda sementara.',
    // 						  'warna_notifikasi' => 'danger',
    // 						  'url_notifikasi' => route('pengaduan').'?view='.$data_pengaduan->id_pengaduan,
    // 						  'status_notifikasi' => 'Delivery',
    // 						  'tgl_notifikasi' => date('Y-m-d H:i:s'),
    // 						);
    // 						DB::table('tb_notifikasi')->insert($values);

    // 					}
    // 				}

    // 				$get_pegawai = DB::table('tb_pegawai')
    // 				->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai]])
    // 				->get();
    // 				if($get_pegawai->count() > 0){
    // 					foreach($get_pegawai as $data_get_pegawai);

    // 					$list_pegawai = DB::table('tb_pegawai')
    // 					->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.sebagai_pegawai','Mitra/Pelanggan'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.kantor_pegawai', $data_get_pegawai->kantor_pegawai],['tb_pegawai.id_bagian_kantor_pusat', $data_get_pegawai->id_bagian_kantor_pusat],['tb_pegawai.id_bagian_kantor_cabang', $data_get_pegawai->id_bagian_kantor_cabang],['tb_pegawai.id_bagian_kantor_wilayah', $data_get_pegawai->id_bagian_kantor_wilayah]])
    // 					->get();

    // 					if($list_pegawai->count() > 0){
    // 						foreach($list_pegawai as $data_list_pegawai){

    // 							$values = array(
    // 							  'id_pegawai' => $data_list_pegawai->id_pegawai,
    // 							  'nama_notifikasi' => 'Pengaduan Holding',
    // 							  'keterangan_notifikasi' => 'Pengaduan "'.$data_pengaduan->nama_pengaduan.'" tertunda sementara.',
    // 							  'warna_notifikasi' => 'danger',
    // 							  'url_notifikasi' => route('pengaduan').'?view='.$data_pengaduan->id_pengaduan,
    // 							  'status_notifikasi' => 'Delivery',
    // 							  'tgl_notifikasi' => date('Y-m-d H:i:s'),
    // 							);
    // 							DB::table('tb_notifikasi')->insert($values);

    // 						}
    // 					}

    // 					// notif chat mitra
    // 					$list_pegawai = DB::table('tb_pegawai')
    // 					->join('tb_kepala_unit_kerja','tb_kepala_unit_kerja.id_pegawai','=','tb_pegawai.id_pegawai')
    // 					->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.sebagai_pegawai','Mitra/Pelanggan'],['tb_pegawai.level_pegawai','Kepala Unit Kerja'],['tb_pegawai.status_pegawai','Aktif'],['tb_kepala_unit_kerja.kantor_pegawai', $data_get_pegawai->kantor_pegawai],['tb_kepala_unit_kerja.id_bagian_kantor_pusat', $data_get_pegawai->id_bagian_kantor_pusat],['tb_kepala_unit_kerja.id_bagian_kantor_cabang', $data_get_pegawai->id_bagian_kantor_cabang],['tb_kepala_unit_kerja.id_bagian_kantor_wilayah', $data_get_pegawai->id_bagian_kantor_wilayah]])
    // 					->groupBy('tb_pegawai.id_pegawai')
    // 					->get();

    // 					if($list_pegawai->count() > 0){
    // 						foreach($list_pegawai as $data_list_pegawai){

    // 							$values = array(
    // 							  'id_pegawai' => $data_list_pegawai->id_pegawai,
    // 							  'nama_notifikasi' => 'Pengaduan Holding',
    // 							  'keterangan_notifikasi' => 'Pengaduan "'.$data_pengaduan->nama_pengaduan.'" tertunda sementara.',
    // 							  'warna_notifikasi' => 'danger',
    // 							  'url_notifikasi' => route('pengaduan').'?view='.$data_pengaduan->id_pengaduan,
    // 							  'status_notifikasi' => 'Delivery',
    // 							  'tgl_notifikasi' => date('Y-m-d H:i:s'),
    // 							);
    // 							DB::table('tb_notifikasi')->insert($values);

    // 						}
    // 					}
    // 					// end notif chat mitra
    // 				}

    // 			}else{

    // 				$where = array(
    // 				  'id_pengaduan' => $id,
    // 				  'delete_pengaduan' => 'N',
    // 				);
    // 				$values = array(
    // 				  'status_pengaduan' => 'On Progress',
    // 				);
    // 				DB::table('tb_pengaduan')->where($where)->update($values);

    // 				$list_pegawai = DB::table('tb_pegawai')
    // 				->where('tb_pegawai.delete_pegawai','=','N')
    // 				->where('tb_pegawai.status_pegawai','=','Aktif')
    // 				->where(function ($query) { $query->where('tb_pegawai.sebagai_pegawai','=','Petugas')
    // 				->orWhere('tb_pegawai.sebagai_pegawai','=','Agent');})
    // 				->where('tb_pegawai.kantor_pegawai','=', $data_pengaduan->kantor_pengaduan)
    // 				->where('tb_pegawai.id_bagian_kantor_pusat','=', $data_pengaduan->id_bagian_kantor_pusat)
    // 				->where('tb_pegawai.id_bagian_kantor_cabang','=', $data_pengaduan->id_bagian_kantor_cabang)
    // 				->where('tb_pegawai.id_bagian_kantor_wilayah','=', $data_pengaduan->id_bagian_kantor_wilayah)
    // 				->get();
    // 				if($list_pegawai->count() > 0){
    // 					foreach($list_pegawai as $data_list_pegawai){

    // 						$values = array(
    // 						  'id_pegawai' => $data_list_pegawai->id_pegawai,
    // 						  'nama_notifikasi' => 'Pengaduan On Progress',
    // 						  'keterangan_notifikasi' => 'Pengaduan "'.$data_pengaduan->nama_pengaduan.'" sedang dalam proses.',
    // 						  'warna_notifikasi' => 'primary',
    // 						  'url_notifikasi' => route('pengaduan').'?view='.$data_pengaduan->id_pengaduan,
    // 						  'status_notifikasi' => 'Delivery',
    // 						  'tgl_notifikasi' => date('Y-m-d H:i:s'),
    // 						);
    // 						DB::table('tb_notifikasi')->insert($values);

    // 					}
    // 				}

    // 				$get_pegawai = DB::table('tb_pegawai')
    // 				->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai]])
    // 				->get();
    // 				if($get_pegawai->count() > 0){
    // 					foreach($get_pegawai as $data_get_pegawai);

    // 					$list_pegawai = DB::table('tb_pegawai')
    // 					->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.sebagai_pegawai','Mitra/Pelanggan'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.kantor_pegawai', $data_get_pegawai->kantor_pegawai],['tb_pegawai.id_bagian_kantor_pusat', $data_get_pegawai->id_bagian_kantor_pusat],['tb_pegawai.id_bagian_kantor_cabang', $data_get_pegawai->id_bagian_kantor_cabang],['tb_pegawai.id_bagian_kantor_wilayah', $data_get_pegawai->id_bagian_kantor_wilayah]])
    // 					->get();

    // 					if($list_pegawai->count() > 0){
    // 						foreach($list_pegawai as $data_list_pegawai){

    // 							$values = array(
    // 							  'id_pegawai' => $data_list_pegawai->id_pegawai,
    // 							  'nama_notifikasi' => 'Pengaduan On Progress',
    // 							  'keterangan_notifikasi' => 'Pengaduan "'.$data_pengaduan->nama_pengaduan.'" sedang dalam proses.',
    // 							  'warna_notifikasi' => 'primary',
    // 							  'url_notifikasi' => route('pengaduan').'?view='.$data_pengaduan->id_pengaduan,
    // 							  'status_notifikasi' => 'Delivery',
    // 							  'tgl_notifikasi' => date('Y-m-d H:i:s'),
    // 							);
    // 							DB::table('tb_notifikasi')->insert($values);

    // 							// $to_email = $data_list_pegawai->email_pegawai;
    // 							// $data = array(
    // 							// 	'id_pengaduan' => $data_pengaduan->id_pengaduan,
    // 							// );

    // 							// Mail::send('pengaduan.email_on_progress', $data, function($message) use ($to_email) {
    // 							// 	$message->to($to_email)
    // 							// 			->subject('Pengaduan Baru (On Progress)');
    // 							// 	$message->from('helpdesk@cnplus.id','Helpdesk');
    // 							// });

    // 							$email_smtp->send('pengaduan.email_on_progress', $data_list_pegawai->email_pegawai, 'Pengaduan Baru (On Progress)', 'id_pengaduan', $data_pengaduan->id_pengaduan);

    // 						}
    // 					}

    // 					// notif chat mitra
    // 					$list_pegawai = DB::table('tb_pegawai')
    // 					->join('tb_kepala_unit_kerja','tb_kepala_unit_kerja.id_pegawai','=','tb_pegawai.id_pegawai')
    // 					->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.sebagai_pegawai','Mitra/Pelanggan'],['tb_pegawai.level_pegawai','Kepala Unit Kerja'],['tb_pegawai.status_pegawai','Aktif'],['tb_kepala_unit_kerja.kantor_pegawai', $data_get_pegawai->kantor_pegawai],['tb_kepala_unit_kerja.id_bagian_kantor_pusat', $data_get_pegawai->id_bagian_kantor_pusat],['tb_kepala_unit_kerja.id_bagian_kantor_cabang', $data_get_pegawai->id_bagian_kantor_cabang],['tb_kepala_unit_kerja.id_bagian_kantor_wilayah', $data_get_pegawai->id_bagian_kantor_wilayah]])
    // 					->groupBy('tb_pegawai.id_pegawai')
    // 					->get();

    // 					if($list_pegawai->count() > 0){
    // 						foreach($list_pegawai as $data_list_pegawai){

    // 							$values = array(
    // 							  'id_pegawai' => $data_list_pegawai->id_pegawai,
    // 							  'nama_notifikasi' => 'Pengaduan On Progress',
    // 							  'keterangan_notifikasi' => 'Pengaduan "'.$data_pengaduan->nama_pengaduan.'" sedang dalam proses.',
    // 							  'warna_notifikasi' => 'primary',
    // 							  'url_notifikasi' => route('pengaduan').'?view='.$data_pengaduan->id_pengaduan,
    // 							  'status_notifikasi' => 'Delivery',
    // 							  'tgl_notifikasi' => date('Y-m-d H:i:s'),
    // 							);
    // 							DB::table('tb_notifikasi')->insert($values);

    // 							// $to_email = $data_list_pegawai->email_pegawai;
    // 							// $data = array(
    // 							// 	'id_pengaduan' => $data_pengaduan->id_pengaduan,
    // 							// );

    // 							// Mail::send('pengaduan.email_on_progress', $data, function($message) use ($to_email) {
    // 							// 	$message->to($to_email)
    // 							// 			->subject('Pengaduan Baru (On Progress)');
    // 							// 	$message->from('helpdesk@cnplus.id','Helpdesk');
    // 							// });

    // 							$email_smtp->send('pengaduan.email_on_progress', $data_list_pegawai->email_pegawai, 'Pengaduan Baru (On Progress)', 'id_pengaduan', $data_pengaduan->id_pengaduan);

    // 						}
    // 					}
    // 					// end notif chat mitra
    // 				}
    // 			}

    // 			return back();

    // 		}

    // 	}
    // }
    
      public function jawaban(Request $request)
    {

        // $email_smtp = new SMTP_Send()   ;
        $email_smtp = null;

        $id = $request->pengaduan;
        $id_pegawai = auth()->user()->id_pegawai;
        $keterangan = nl2br($request->keterangan);
        $foto = 'logos/image.png';
        if (!empty($request->file('foto'))) {
            $file_foto = 'foto_jawaban_' . date('Ymd_His.') . $request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->move('images', $file_foto);
            $foto = url('images/' . $file_foto);
        }
        $sla = $request->sla;
        $durasi_sla = date('Y-m-d H:i:s', strtotime('+' . $request->durasi_sla . ' day'));
        $alasan_sla = nl2br($request->alasan_sla);
        $tgl = date('Y-m-d H:i:s');

        $pengaduan = DB::table('tb_pengaduan')
            ->where([['tb_pengaduan.delete_pengaduan', 'N'], ['tb_pengaduan.id_pengaduan', $id]])
            ->get();

        if ($pengaduan->count() < 1) {
            return back();
        } else {

            foreach ($pengaduan as $data_pengaduan);

            $pegawai = DB::table('tb_pegawai')
                ->where([['tb_pegawai.id_pegawai', $id_pegawai], ['tb_pegawai.kantor_pegawai', $data_pengaduan->kantor_pengaduan], ['tb_pegawai.id_bagian_kantor_pusat', $data_pengaduan->id_bagian_kantor_pusat], ['tb_pegawai.id_bagian_kantor_cabang', $data_pengaduan->id_bagian_kantor_cabang], ['tb_pegawai.id_bagian_kantor_wilayah', $data_pengaduan->id_bagian_kantor_wilayah], ['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif']])
                ->get();

            if ($pegawai->count() < 1) {
                return back();
            } else {

                $values = array(
                    'id_pengaduan' => $id,
                    'id_pegawai' => $id_pegawai,
                    'keterangan_jawaban' => $keterangan,
                    'foto_jawaban' => $foto,
                    'sla_jawaban' => $sla,
                    'durasi_sla_jawaban' => $durasi_sla,
                    'alasan_sla_jawaban' => $alasan_sla,
                    'tgl_jawaban' => $tgl,
                );
                DB::table('tb_jawaban')->insert($values);
            
                    $where = array(
                        'id_pengaduan' => $id,
                        'delete_pengaduan' => 'N',
                    );
                    $values = array(
                        'status_pengaduan' => 'On Progress',
                    );
                    DB::table('tb_pengaduan')->where($where)->update($values);
                     $pegawai = DB::table('tb_pegawai')
                    ->where('tb_pegawai.delete_pegawai', '=', 'N')
                    ->where('tb_pegawai.status_pegawai', '=', 'Aktif')
                    ->where('tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai)
                    ->first();
                $values = array(
                    'id_pegawai' => $pegawai->id_pegawai,
                    'nama_notifikasi' => 'Pengaduan On Progress',
                    'keterangan_notifikasi' => 'Pengaduan "' . $data_pengaduan->nama_pengaduan . '" sedang dalam proses.',
                    'warna_notifikasi' => 'primary',
                    'url_notifikasi' => route('pengaduan') . '?view=' . $data_pengaduan->id_pengaduan,
                    'status_notifikasi' => 'Delivery',
                    'tgl_notifikasi' => date('Y-m-d H:i:s'),
                );
                DB::table('tb_notifikasi')->insert($values);
                $to_email = 'amimfaisal2@gmail.com';
                $data = array(
                    'id_pengaduan' => $data_pengaduan->id_pengaduan,
                );

                Mail::send('pages.pengaduan.email_on_progress', $data, function ($message) use ($to_email) {
                    $message->to($to_email)
                        ->subject('Pengaduan Baru (On Progress)');
                    $message->from('helpdesk@cnplus.id', 'Helpdesk');
                });

                    // $list_pegawai = DB::table('tb_pegawai')
                    //     ->where('tb_pegawai.delete_pegawai', '=', 'N')
                    //     ->where('tb_pegawai.status_pegawai', '=', 'Aktif')
                    //     ->where('tb_pegawai.id_bagian_kantor_pusat', '=', $data_pengaduan->id_bagian_kantor_pusat)
                    //     ->where('tb_pegawai.id_bagian_kantor_cabang', '=', $data_pengaduan->id_bagian_kantor_cabang)
                    //     ->where('tb_pegawai.id_bagian_kantor_wilayah', '=', $data_pengaduan->id_bagian_kantor_wilayah)
                    //     ->get();


                    // if ($list_pegawai->count() > 0) {
                    //     foreach ($list_pegawai as $data_list_pegawai) {

                    //         $values = array(
                    //             'id_pegawai' => $data_list_pegawai->id_pegawai,
                    //             'nama_notifikasi' => 'Pengaduan On Progress',
                    //             'keterangan_notifikasi' => 'Pengaduan "' . $data_pengaduan->nama_pengaduan . '" sedang dalam proses.',
                    //             'warna_notifikasi' => 'primary',
                    //             'url_notifikasi' => route('pengaduan') . '?view=' . $data_pengaduan->id_pengaduan,
                    //             'status_notifikasi' => 'Delivery',
                    //             'tgl_notifikasi' => date('Y-m-d H:i:s'),
                    //         );
                    //         DB::table('tb_notifikasi')->insert($values);
                    //     }
                    // }

                    // $get_pegawai = DB::table('tb_pegawai')
                    //     ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai]])
                    //     ->get();
                    // if ($get_pegawai->count() > 0) {
                    //     foreach ($get_pegawai as $data_get_pegawai);

                    //     $list_pegawai = DB::table('tb_pegawai')
                    //         ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.sebagai_pegawai', 'Mitra/Pelanggan'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.kantor_pegawai', $data_get_pegawai->kantor_pegawai], ['tb_pegawai.id_bagian_kantor_pusat', $data_get_pegawai->id_bagian_kantor_pusat], ['tb_pegawai.id_bagian_kantor_cabang', $data_get_pegawai->id_bagian_kantor_cabang], ['tb_pegawai.id_bagian_kantor_wilayah', $data_get_pegawai->id_bagian_kantor_wilayah]])
                    //         ->get();

                    //     if ($list_pegawai->count() > 0) {
                    //         foreach ($list_pegawai as $data_list_pegawai) {

                    //             $values = array(
                    //                 'id_pegawai' => $data_list_pegawai->id_pegawai,
                    //                 'nama_notifikasi' => 'Pengaduan On Progress',
                    //                 'keterangan_notifikasi' => 'Pengaduan "' . $data_pengaduan->nama_pengaduan . '" sedang dalam proses.',
                    //                 'warna_notifikasi' => 'primary',
                    //                 'url_notifikasi' => route('pengaduan') . '?view=' . $data_pengaduan->id_pengaduan,
                    //                 'status_notifikasi' => 'Delivery',
                    //                 'tgl_notifikasi' => date('Y-m-d H:i:s'),
                    //             );
                    //             DB::table('tb_notifikasi')->insert($values);

                    //             $to_email = $data_list_pegawai->email_pegawai;
                    //             $data = array(
                    //                 'id_pengaduan' => $data_pengaduan->id_pengaduan,
                    //             );

                    //             // Mail::send('pengaduan.email_on_progress', $data, function($message) use ($to_email) {
                    //             // 	$message->to($to_email)
                    //             // 			->subject('Pengaduan Baru (On Progress)');
                    //             // 	$message->from('helpdesk@cnplus.id','Helpdesk');
                    //             // });

                    //             // $email_smtp->send('pengaduan.email_on_progress', $data_list_pegawai->email_pegawai, 'Pengaduan Baru (On Progress)', 'id_pengaduan', $data_pengaduan->id_pengaduan);

                    //         }
                    //     }

                        // // notif chat mitra
                        // $list_pegawai = DB::table('tb_pegawai')
                        //     ->join('tb_kepala_unit_kerja', 'tb_kepala_unit_kerja.id_pegawai', '=', 'tb_pegawai.id_pegawai')
                        //     ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.sebagai_pegawai', 'Mitra/Pelanggan'], ['tb_pegawai.level_pegawai', 'Kepala Unit Kerja'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_kepala_unit_kerja.kantor_pegawai', $data_get_pegawai->kantor_pegawai], ['tb_kepala_unit_kerja.id_bagian_kantor_pusat', $data_get_pegawai->id_bagian_kantor_pusat], ['tb_kepala_unit_kerja.id_bagian_kantor_cabang', $data_get_pegawai->id_bagian_kantor_cabang], ['tb_kepala_unit_kerja.id_bagian_kantor_wilayah', $data_get_pegawai->id_bagian_kantor_wilayah]])
                        //     ->groupBy('tb_pegawai.id_pegawai')
                        //     ->get();

                        // if ($list_pegawai->count() > 0) {
                        //     foreach ($list_pegawai as $data_list_pegawai) {

                        //         $values = array(
                        //             'id_pegawai' => $data_list_pegawai->id_pegawai,
                        //             'nama_notifikasi' => 'Pengaduan On Progress',
                        //             'keterangan_notifikasi' => 'Pengaduan "' . $data_pengaduan->nama_pengaduan . '" sedang dalam proses.',
                        //             'warna_notifikasi' => 'primary',
                        //             'url_notifikasi' => route('pengaduan') . '?view=' . $data_pengaduan->id_pengaduan,
                        //             'status_notifikasi' => 'Delivery',
                        //             'tgl_notifikasi' => date('Y-m-d H:i:s'),
                        //         );
                        //         DB::table('tb_notifikasi')->insert($values);

                        //         // $to_email = $data_list_pegawai->email_pegawai;
                        //         // $data = array(
                        //         // 	'id_pengaduan' => $data_pengaduan->id_pengaduan,
                        //         // );

                        //         // Mail::send('pengaduan.email_on_progress', $data, function($message) use ($to_email) {
                        //         // 	$message->to($to_email)
                        //         // 			->subject('Pengaduan Baru (On Progress)');
                        //         // 	$message->from('helpdesk@cnplus.id','Helpdesk');
                        //         // });

                        //         // $email_smtp->send('pengaduan.email_on_progress', $data_list_pegawai->email_pegawai, 'Pengaduan Baru (On Progress)', 'id_pengaduan', $data_pengaduan->id_pengaduan);

                        //     }
                        // }
                        // end notif chat mitra
                    // }
                // }

            }
                return back();
        }
    }


    public function tanggapan (Request $request){
    	$id = $request->pengaduan;

    	$pengaduan = DB::table('tb_pengaduan')
    	->where([['tb_pengaduan.delete_pengaduan','N'],['tb_pengaduan.id_pengaduan', $id],['tb_pengaduan.id_pegawai', Session::get('id_pegawai')]])
    	->get();

    	if($pengaduan->count() < 1){
    		return back();
    	}else{

    		foreach($pengaduan as $data_pengaduan);

    		$jawaban = $request->jawaban;
    		$keterangan = nl2br($request->keterangan);
    		$foto = 'logos/image.png';
    		if(!empty($request->file('foto'))){
    		  $file_foto = 'foto_tanggapan_'.date('Ymd_His.').$request->file('foto')->getClientOriginalExtension();
    		  $request->file('foto')->move('images', $file_foto);
    		  $foto = url('images/'.$file_foto);
    		}
    		$tgl = date('Y-m-d H:i:s');

    		$values = array(
    		  'id_jawaban' => $jawaban,
    		  'keterangan_tanggapan' => $keterangan,
    		  'id_pegawai' => auth()->user()->id_pegawai,
    		  'foto_tanggapan' => $foto,
    		  'tgl_tanggapan' => $tgl,
    		);
    		DB::table('tb_tanggapan')->insert($values);
            $pegawai = DB::table('tb_pegawai')
                    ->where('tb_pegawai.delete_pegawai', '=', 'N')
                    ->where('tb_pegawai.status_pegawai', '=', 'Aktif')
                    ->where('tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai)
                    ->first();
          if ($pegawai->id_bagian_kantor_pusat != 0) {
                    $kantor_pusat = BagianKantorPusat::where('id_bagian_kantor_pusat', $pegawai->id_bagian_kantor_pusat)->where('delete_bagian_kantor_pusat', 'N')->first();
                    $list_pegawai = Pegawai::where('id_bagian_kantor_pusat', $kantor_pusat->id_bagian_kantor_pusat)->where('delete_pegawai', 'N')->get();
                } elseif ($pegawai->id_bagian_kantor_cabang != 0) {
                    $kantor_cabang = BagianKantorCabang::where('id_bagian_kantor_cabang', $pegawai->id_bagian_kantor_cabang)->where('delete_bagian_kantor_cabang', 'N')->first();
                    $list_pegawai = Pegawai::where('id_bagian_kantor_cabang', $kantor_cabang->id_bagian_kantor_cabang)->where('delete_pegawai', 'N')->get();
                } elseif ($pegawai->id_bagian_kantor_wilayah != 0) {
                    $kantor_wilayah = BagianKantorwilayah::where('id_bagian_kantor_wilayah', $pegawai->id_bagian_kantor_wilayah)->where('delete_bagian_kantor_wilayah', 'N')->first();
                    $list_pegawai = Pegawai::where('id_bagian_kantor_wilayah', $kantor_wilayah->id_bagian_kantor_wilayah)->where('delete_pegawai', 'N')->get();
                }

                    if ($list_pegawai->count() > 0) {
                        foreach ($list_pegawai as $data_list_pegawai) {

                            $values = array(
                                'id_pegawai' => $data_list_pegawai->id_pegawai,
                                'nama_notifikasi' => 'Pengaduan On Progress',
                                'keterangan_notifikasi' => 'Pengaduan "' . $data_pengaduan->nama_pengaduan . '" sedang dalam proses.',
                                'warna_notifikasi' => 'primary',
                                'url_notifikasi' => route('pengaduan') . '?view=' . $data_pengaduan->id_pengaduan,
                                'status_notifikasi' => 'Delivery',
                                'tgl_notifikasi' => date('Y-m-d H:i:s'),
                            );
                            DB::table('tb_notifikasi')->insert($values);
                        }
                    }
    // 		$list_pegawai = DB::table('tb_pegawai')
    // 		->where('tb_pegawai.delete_pegawai','=','N')
    // 		->where('tb_pegawai.status_pegawai','=','Aktif')
    // 		->where(function ($query) { $query->where('tb_pegawai.sebagai_pegawai','=','Petugas')
    // 		->orWhere('tb_pegawai.sebagai_pegawai','=','Agent');})
    // 		->where('tb_pegawai.kantor_pegawai','=', $data_pengaduan->kantor_pengaduan)
    // 		->where('tb_pegawai.id_bagian_kantor_pusat','=', $data_pengaduan->id_bagian_kantor_pusat)
    // 		->where('tb_pegawai.id_bagian_kantor_cabang','=', $data_pengaduan->id_bagian_kantor_cabang)
    // 		->where('tb_pegawai.id_bagian_kantor_wilayah','=', $data_pengaduan->id_bagian_kantor_wilayah)
    // 		->get();
    // 		if($list_pegawai->count() > 0){
    // 			foreach($list_pegawai as $data_list_pegawai){

    // 				$values = array(
    // 				  'id_pegawai' => $data_list_pegawai->id_pegawai,
    // 				  'nama_notifikasi' => 'Pengaduan Tanggapan',
    // 				  'keterangan_notifikasi' => 'Tanggapan pengaduan "'.$data_pengaduan->nama_pengaduan.'" dari Mitra/Pelanggan.',
    // 				  'warna_notifikasi' => 'info',
    // 				  'url_notifikasi' => route('pengaduan').'?view='.$data_pengaduan->id_pengaduan,
    // 				  'status_notifikasi' => 'Delivery',
    // 				  'tgl_notifikasi' => date('Y-m-d H:i:s'),
    // 				);
    // 				DB::table('tb_notifikasi')->insert($values);

    // 			}
    // 		}

    		return back();
    	}
    }

    public function form_tanggapan (Request $request){
    	$id_jawaban = $request->id_jawaban;
    	$id_pengaduan = 0;

    	$jawaban = DB::table('tb_jawaban')
    	->where('tb_jawaban.id_jawaban','=', $id_jawaban)
    	->get();
    	if($jawaban->count() > 0){
    		foreach($jawaban as $data_jawaban);
    		$id_pengaduan = $data_jawaban->id_pengaduan;
    	}

    	return view('pages.pengaduan.form_tanggapan', compact('id_jawaban', 'id_pengaduan'));
    }

    //   public function checked (Request $request){

    // 	$id = $request->pengaduan;
    // 	$pengaduan = DB::table('tb_pengaduan')
    // 	->where([['tb_pengaduan.delete_pengaduan','N'],['tb_pengaduan.status_pengaduan','Pending'],['tb_pengaduan.id_pengaduan', $id]])
    // 	->get();
    //     // dd($pengaduan);

    // 	if($pengaduan->count() < 1){
    // 		return back();
    // 	}else{
    // 		foreach($pengaduan as $data_pengaduan){
    // 			// get data pegawai
    // 			$pegawai = DB::table('tb_pegawai')
    // 			->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai]])
    // 			->get();
    // 			if($pegawai->count() > 0){
    // 				foreach($pegawai as $data_pegawai);

    // 				$get_pegawai = DB::table('tb_pegawai')
    // 				->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.sebagai_pegawai','Mitra/Pelanggan'],['tb_pegawai.level_pegawai','Kepala Bagian Unit Kerja'],['tb_pegawai.id_pegawai', auth()->user()->id_pegawai],['tb_pegawai.kantor_pegawai', $data_pegawai->kantor_pegawai],['tb_pegawai.id_bagian_kantor_pusat', $data_pegawai->id_bagian_kantor_pusat],['tb_pegawai.id_bagian_kantor_cabang', $data_pegawai->id_bagian_kantor_cabang],['tb_pegawai.id_bagian_kantor_wilayah', $data_pegawai->id_bagian_kantor_wilayah]])
    // 				->get();

    // 				if($get_pegawai->count() > 0){
    // 					foreach($get_pegawai as $data_get_pegawai){
    // 						$values = array(
    // 						  'id_pengaduan' => $id,
    // 						  'id_pegawai' => $data_get_pegawai->id_pegawai,
    // 						  'tgl_mengetahui' => date('Y-m-d H:i:s'),
    // 						);
    // 						DB::table('tb_mengetahui')->insert($values);

    // 						$where = array(
    // 						  'id_pengaduan' => $id,
    // 						  'delete_pengaduan' => 'N',
    // 						);
    // 						$values = array(
    // 						  'status_pengaduan' => 'Checked',
    // 						);
    // 						DB::table('tb_pengaduan')->where($where)->update($values);

    // 						$get_pegawai = DB::table('tb_pegawai')
    // 						->join('tb_kepala_unit_kerja','tb_kepala_unit_kerja.id_pegawai','=','tb_pegawai.id_pegawai')
    // 						->where('tb_pegawai.id_pegawai','!=', $data_pegawai->id_pegawai)
    // 						->where('tb_pegawai.delete_pegawai','=','N')
    // 						->where('tb_pegawai.status_pegawai','=','Aktif')
    // 						->where(function ($query) { $query->where('tb_pegawai.level_pegawai','=','Kepala Unit Kerja')
    // 						->orWhere('tb_pegawai.level_pegawai','=','Kepala Unit Kerja');})
    // 						->where('tb_kepala_unit_kerja.id_bagian_kantor_pusat','=', $data_pegawai->id_bagian_kantor_pusat)
    // 						->where('tb_kepala_unit_kerja.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
    // 						->where('tb_kepala_unit_kerja.id_bagian_kantor_wilayah','=', $data_pegawai->id_bagian_kantor_wilayah)
    // 						->groupBy('tb_pegawai.id_pegawai')
    // 						->get();
    //                         // dd($get_pegawai);
    // 						if($get_pegawai->count() > 0){
    // 							foreach($get_pegawai as $data_get_pegawai){
    // 								$values = array(
    // 								  'id_pegawai' => $data_get_pegawai->id_pegawai,
    // 								  'nama_notifikasi' => 'Pengaduan Checked',
    // 								  'keterangan_notifikasi' => 'Pengaduan baru "'.$data_pengaduan->nama_pengaduan.'" telah diajukan oleh '.$data_pegawai->nama_pegawai,
    // 								  'warna_notifikasi' => 'warning',
    // 								  'url_notifikasi' => route('pengaduan').'?view='.$data_pengaduan->id_pengaduan,
    // 								  'status_notifikasi' => 'Delivery',
    // 								  'tgl_notifikasi' => date('Y-m-d H:i:s'),
    // 								);
    // 								DB::table('tb_notifikasi')->insert($values);

    // 								$to_email = $data_get_pegawai->email_pegawai;
    // 								$data = array(
    // 									'id_pengaduan' => $data_pengaduan->id_pengaduan,
    // 								);

    // 								// Mail::send('pengaduan.email_checked', $data, function($message) use ($to_email) {
    // 								// 	$message->to($to_email)
    // 								// 			->subject('Pengaduan Baru (Checked)');
    // 								// 	$message->from('helpdesk@cnplus.id','Helpdesk');
    // 								// });
    // 							}
    // 						}

    // 						return back()->with('alert', 'success_Pengaduan telah di checked.');
    // 					}
    // 				}else{
    // 					return back();
    // 				}

    // 			}else{
    // 				return back();
    // 			}
    // 			// end get data pegawai
    // 		}
    // 	}

    // }
      public function checked(Request $request)
    {

        $id = $request->pengaduan;
        $pengaduan = DB::table('tb_pengaduan')
            ->where([['tb_pengaduan.delete_pengaduan', 'N'], ['tb_pengaduan.status_pengaduan', 'Pending'], ['tb_pengaduan.id_pengaduan', $id]])
            ->get();


        if ($pengaduan->count() < 1) {
            return back();
        } else {
            foreach ($pengaduan as $data_pengaduan) {
                // get data pegawai
                $pegawai = DB::table('tb_pegawai')
                    ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai]])
                    ->get();

                if ($pegawai->count() > 0) {
                    foreach ($pegawai as $data_pegawai);

                    $get_pegawai = DB::table('tb_pegawai')
                        ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.level_pegawai', 'Kepala Bagian Unit Kerja'], ['tb_pegawai.id_pegawai', auth()->user()->id_pegawai], ['tb_pegawai.kantor_pegawai', $data_pegawai->kantor_pegawai], ['tb_pegawai.id_bagian_kantor_pusat', $data_pegawai->id_bagian_kantor_pusat], ['tb_pegawai.id_bagian_kantor_cabang', $data_pegawai->id_bagian_kantor_cabang], ['tb_pegawai.id_bagian_kantor_wilayah', $data_pegawai->id_bagian_kantor_wilayah]])
                        ->get();


                    if ($get_pegawai->count() > 0) {
                        foreach ($get_pegawai as $data_get_pegawai) {
                            $values = array(
                                'id_pengaduan' => $id,
                                'id_pegawai' => $data_get_pegawai->id_pegawai,
                                'tgl_mengetahui' => date('Y-m-d H:i:s'),
                            );
                            DB::table('tb_mengetahui')->insert($values);

                            $where = array(
                                'id_pengaduan' => $id,
                                'delete_pengaduan' => 'N',
                            );
                            $values = array(
                                'status_pengaduan' => 'Checked',
                                'checked_pengaduan' => $data_get_pegawai->id_pegawai,
                            );
                            DB::table('tb_pengaduan')->where($where)->update($values);

                            $values = array(
                                'id_pengaduan' => $data_pengaduan->id_pengaduan,
                                'id_pegawai' => auth()->user()->id_pegawai,
                                'created_by' => auth()->user()->nama_pegawai
                            );
                            Checked::create($values);
                            $get_pegawai = DB::table('tb_pegawai')
                                ->where('tb_pegawai.id_pegawai', '!=', $data_pegawai->id_pegawai)
                                ->where('tb_pegawai.delete_pegawai', '=', 'N')
                                ->where('tb_pegawai.status_pegawai', '=', 'Aktif')
                                ->where('tb_pegawai.level_pegawai', 'Kepala Bagian Unit Kerja')
                                ->get();

                            if ($get_pegawai->count() > 0) {
                                foreach ($get_pegawai as $data_get_pegawai) {
                                    $values = array(
                                        'id_pegawai' => $data_get_pegawai->id_pegawai,
                                        'nama_notifikasi' => 'Pengaduan Checked',
                                        'keterangan_notifikasi' => 'Pengaduan baru "' . $data_pengaduan->nama_pengaduan . '" telah diajukan oleh ' . $data_pegawai->nama_pegawai,
                                        'warna_notifikasi' => 'warning',
                                        'url_notifikasi' => route('pengaduan') . '?view=' . $data_pengaduan->id_pengaduan,
                                        'status_notifikasi' => 'Delivery',
                                        'tgl_notifikasi' => date('Y-m-d H:i:s'),
                                    );
                                    DB::table('tb_notifikasi')->insert($values);

                                    // $to_email = $data_get_pegawai->email_pegawai;
                                    $to_email = 'amimfaisal2@gmail.com';
                                    $data = array(
                                        'id_pengaduan' => $data_pengaduan->id_pengaduan,
                                    );

                                    Mail::send('pages.pengaduan.email_checked', $data, function ($message) use ($to_email) {
                                        $message->to($to_email)
                                            ->subject('Pengaduan Baru (Checked)');
                                        $message->from('helpdesk@cnplus.id', 'Helpdesk');
                                    });
                                }
                            }

                            return back()->with('alert', 'success_Pengaduan telah di checked.');
                        }
                    } else {
                        return back();
                    }
                } else {
                    return back();
                }
                // end get data pegawai
            }
        }
    }
    
    
   public function data_grid_friend()
    {
        $session_pegawai = Pegawai::where('delete_pegawai', 'N')->where('id_pegawai', auth()->user()->id_pegawai)->get();
        if ($session_pegawai->count() > 0) {
            # code...
            foreach ($session_pegawai as $key => $data_session_pegawai) {
                if ($data_session_pegawai->level_pegawai == 'Administrator') {
                    $pengaduan = Pengaduan::where('id_bagian_kantor_cabang', $data_session_pegawai->id_bagian_kantor_cabang)->where('id_bagian_kantor_pusat', $data_session_pegawai->id_bagian_kantor_pusat)->where('id_bagian_kantor_wilayah', $data_session_pegawai->id_bagian_kantor_wilayah)->where('delete_pengaduan', 'N')->where(function ($query) {
                        $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                    })->where('delete_pengaduan', 'N')->paginate(12);
                } else if ($data_session_pegawai->level_pegawai == 'Kepala Unit Kerja') {
                    $kepala_unit = KepalaUnit::where('id_pegawai', $data_session_pegawai->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
                    if ($kepala_unit->id_bagian_kantor_pusat != 0) {
                        $pengaduan = pengaduan::with('BagianKantorPusat')->where('id_bagian_kantor_pusat', $kepala_unit->id_bagian_kantor_pusat)
                            ->where('delete_pengaduan', 'N')->where(function ($query) {
                                $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress')->orWhere('tb_pengaduan.status_pengaduan', '=', 'Moving');
                            })->orderBy('tgl_pengaduan', 'desc')->paginate(12);
                    } elseif ($kepala_unit->id_bagian_kantor_cabang != 0) {
                        $pengaduan = pengaduan::with('BagianKantorCabang')->where('id_bagian_kantor_cabang', $kepala_unit->id_bagian_kantor_cabang)
                            ->where('delete_pengaduan', 'N')->where(function ($query) {
                                $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                            })->orderBy('tgl_pengaduan', 'desc')->paginate(12);
                    } elseif ($kepala_unit->id_bagian_kantor_wilayah != 0) {
                        $pengaduan = pengaduan::with('BagianKantorWilayah')->where('id_bagian_kantor_wilayah', $kepala_unit->id_bagian_kantor_wilayah)
                            ->where('delete_pengaduan', 'N')->where(function ($query) {
                                $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                            })->orderBy('tgl_pengaduan', 'desc')->paginate(12);
                    }
                    // dd($pengaduan);
                } else if ($data_session_pegawai->level_pegawai == 'Staff' || $data_session_pegawai->level_pegawai == 'Kepala Bagian Unit Kerja') {
                    if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                        $pengaduan = pengaduan::with('BagianKantorPusat')->where('id_bagian_kantor_pusat', $data_session_pegawai->id_bagian_kantor_pusat)
                            ->where('delete_pengaduan', 'N')->where(function ($query) {
                                $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress')->orWhere('tb_pengaduan.status_pengaduan', '=', 'Moving');
                            })->orderBy('tgl_pengaduan', 'desc')->paginate(12);
                    } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {
                        $pengaduan = pengaduan::with('BagianKantorCabang')->where('id_bagian_kantor_cabang', $data_session_pegawai->id_bagian_kantor_cabang)
                            ->where('delete_pengaduan', 'N')->where(function ($query) {
                                $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress')->orWhere('tb_pengaduan.status_pengaduan', '=', 'Moving');
                            })->orderBy('tgl_pengaduan', 'desc')->paginate(12);
                    } elseif ($data_session_pegawai->id_bagian_kantor_wilayah != 0) {
                        $pengaduan = pengaduan::with('BagianKantorWilayah')->where('id_bagian_kantor_wilayah', $data_session_pegawai->id_bagian_kantor_wilayah)
                            ->where('delete_pengaduan', 'N')->where(function ($query) {
                                $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress')->orWhere('tb_pengaduan.status_pengaduan', '=', 'Moving');
                            })->orderBy('tgl_pengaduan', 'desc')->paginate(12);
                    }
                    // dd($pengaduan);
                }
            }
        }
        return view('pages.pengaduan.data_grid_from', compact('pengaduan', 'data_session_pegawai'))->render();
    }
    public function friend()
    {
        $session_pegawai = Pegawai::where('delete_pegawai', 'N')->where('id_pegawai', auth()->user()->id_pegawai)->get();

        if ($session_pegawai->count() > 0) {
            foreach ($session_pegawai as $key => $data_session_pegawai) {
                if ($data_session_pegawai->level_pegawai == 'Administrator') {
                    $pengaduan = Pengaduan::where('id_bagian_kantor_cabang', $data_session_pegawai->id_bagian_kantor_cabang)->where('id_bagian_kantor_pusat', $data_session_pegawai->id_bagian_kantor_pusat)->where('id_bagian_kantor_wilayah', $data_session_pegawai->id_bagian_kantor_wilayah)->where('delete_pengaduan', 'N')->where(function ($query) {
                        $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                    })->where('delete_pengaduan', 'N')->paginate(12);
                } else if ($data_session_pegawai->level_pegawai == 'Kepala Unit Kerja') {
                    $kepala_unit = KepalaUnit::where('id_pegawai', $data_session_pegawai->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
                    if ($kepala_unit->id_bagian_kantor_pusat != 0) {
                        $pengaduan = pengaduan::with('BagianKantorPusat')->where('id_bagian_kantor_pusat', $kepala_unit->id_bagian_kantor_pusat)
                            ->where('delete_pengaduan', 'N')->where(function ($query) {
                                $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                            })->paginate(12);
                    } elseif ($kepala_unit->id_bagian_kantor_cabang != 0) {
                        $pengaduan = pengaduan::with('BagianKantorCabang')->where('id_bagian_kantor_cabang', $kepala_unit->id_bagian_kantor_cabang)
                            ->where('delete_pengaduan', 'N')->where(function ($query) {
                                $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                            })->paginate(12);
                    } elseif ($kepala_unit->id_bagian_kantor_wilayah != 0) {
                        $pengaduan = pengaduan::with('BagianKantorWilayah')->where('id_bagian_kantor_wilayah', $kepala_unit->id_bagian_kantor_wilayahweb)
                            ->where('delete_pengaduan', 'N')->where(function ($query) {
                                $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                            })->paginate(12);
                    }
                    // dd($pengaduan);
                } else if ($data_session_pegawai->level_pegawai == 'Staff' || $data_session_pegawai->level_pegawai == 'Kepala Bagian Unit Kerja') {
                    if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                        $pengaduan = pengaduan::with('BagianKantorPusat')->where('id_bagian_kantor_pusat', $data_session_pegawai->id_bagian_kantor_pusat)
                            ->where('delete_pengaduan', 'N')->where(function ($query) {
                                $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress')->orWhere('tb_pengaduan.status_pengaduan', '=', 'Moving');
                            })->paginate(12);
                    } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {
                        $pengaduan = pengaduan::with('BagianKantorCabang')->where('id_bagian_kantor_cabang', $data_session_pegawai->id_bagian_kantor_cabang)
                            ->where('delete_pengaduan', 'N')->where(function ($query) {
                                $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress')->orWhere('tb_pengaduan.status_pengaduan', '=', 'Moving');
                            })->paginate(12);
                    } elseif ($data_session_pegawai->id_bagian_kantor_wilayah != 0) {
                        $pengaduan = pengaduan::with('BagianKantorWilayah')->where('id_bagian_kantor_wilayah', $data_session_pegawai->id_bagian_kantor_wilayah)
                            ->where('delete_pengaduan', 'N')->where(function ($query) {
                                $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress')->orWhere('tb_pengaduan.status_pengaduan', '=', 'Moving');
                            })->paginate(12);
                    }
                }
            }
        }
        return view('pages.pengaduan.pengaduan_friend', compact('pengaduan'));
    }

    public function klasifikasi(Request $request)
    {
        try {
            $pengaduan = Pengaduan::where('id_pengaduan', $request->pengaduanId)->where('delete_pengaduan', 'N')->first();
            $pengaduan->klasifikasi_pengaduan = $request->klasifikasi;
            $pengaduan->update();
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $th) {
            //throw $th;
            return response()->json(['status' => $th->getMessage()], 400);
        }
    }

}
