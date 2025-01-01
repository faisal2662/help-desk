<?php

namespace App\Http\Controllers;

use DB;
use Mail;
use Image;
use Session;
use Socialite;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RoleAccountController;

class PelangganController extends Controller
{

    function __construct()
    {
        $this->role = new RoleAccountController();
        $this->route = "pelanggan";
    }

    public function index()
    {
        $role = $this->role->role(Auth::user()->id_pegawai, "", $this->route);


        if ($role->can_create == "Y") {
            $input = '<a href="?create=data">
								<span class="badge badge-primary">
								  <i class="bx bx-plus"></i> User
								</span>
							</a>';
        } else {
            $input = "";
        }

        return view('pages.pelanggan.index', compact('input'));
    }

    public function datatables(Request $request)
    {
        $role = $this->role->role(Auth::user()->id_pegawai, "", $this->route);

        $pelanggan = DB::table('tb_pegawai')
            ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.sebagai_pegawai', 'Mitra/Pelanggan']])
            ->orderBy('tb_pegawai.id_pegawai', 'DESC')
            ->get();

        $no = 1;
        foreach ($pelanggan as $data) {

            $kantor = '-';
            $bagian = '-';

            if ($data->kantor_pegawai == 'Kantor Pusat') {

                $unit_kerja = DB::table('tb_kepala_unit_kerja')
                    ->where([['tb_kepala_unit_kerja.id_pegawai', $data->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N'], ['tb_kepala_unit_kerja.kantor_pegawai', $data->kantor_pegawai]])
                    ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
                    ->limit(1)
                    ->get();

                if ($unit_kerja->count() > 0) {
                    foreach ($unit_kerja as $data_unit_kerja) {

                        $kantor_bagian = DB::table('tb_bagian_kantor_pusat')
                            ->join('tb_kantor_pusat', 'tb_kantor_pusat.id_kantor_pusat', '=', 'tb_bagian_kantor_pusat.id_kantor_pusat')
                            ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data_unit_kerja->id_bagian_kantor_pusat)
                            ->get();
                        if ($kantor_bagian->count() > 0) {
                            foreach ($kantor_bagian as $data_kantor_bagian) {
                                $kantor = $data_kantor_bagian->nama_kantor_pusat;
                                $bagian = 'Semua Bagian';
                            }
                        }
                    }
                } else {

                    $kantor_bagian = DB::table('tb_bagian_kantor_pusat')
                        ->join('tb_kantor_pusat', 'tb_kantor_pusat.id_kantor_pusat', '=', 'tb_bagian_kantor_pusat.id_kantor_pusat')
                        ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data->id_bagian_kantor_pusat)
                        ->get();
                    if ($kantor_bagian->count() > 0) {
                        foreach ($kantor_bagian as $data_kantor_bagian) {
                            $kantor = $data_kantor_bagian->nama_kantor_pusat;
                            $bagian = $data_kantor_bagian->nama_bagian_kantor_pusat;
                        }
                    }
                }
            } else if ($data->kantor_pegawai == 'Kantor Cabang') {

                $unit_kerja = DB::table('tb_kepala_unit_kerja')
                    ->where([['tb_kepala_unit_kerja.id_pegawai', $data->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N'], ['tb_kepala_unit_kerja.kantor_pegawai', $data->kantor_pegawai]])
                    ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
                    ->limit(1)
                    ->get();

                if ($unit_kerja->count() > 0) {
                    foreach ($unit_kerja as $data_unit_kerja) {

                        $kantor_bagian = DB::table('tb_bagian_kantor_cabang')
                            ->join('tb_kantor_cabang', 'tb_kantor_cabang.id_kantor_cabang', '=', 'tb_bagian_kantor_cabang.id_kantor_cabang')
                            ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data_unit_kerja->id_bagian_kantor_cabang)
                            ->get();
                        if ($kantor_bagian->count() > 0) {
                            foreach ($kantor_bagian as $data_kantor_bagian) {
                                $kantor = $data_kantor_bagian->nama_kantor_cabang;
                                $bagian = 'Semua Bagian';
                            }
                        }
                    }
                } else {

                    $kantor_bagian = DB::table('tb_bagian_kantor_cabang')
                        ->join('tb_kantor_cabang', 'tb_kantor_cabang.id_kantor_cabang', '=', 'tb_bagian_kantor_cabang.id_kantor_cabang')
                        ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data->id_bagian_kantor_cabang)
                        ->get();
                    if ($kantor_bagian->count() > 0) {
                        foreach ($kantor_bagian as $data_kantor_bagian) {
                            $kantor = $data_kantor_bagian->nama_kantor_cabang;
                            $bagian = $data_kantor_bagian->nama_bagian_kantor_cabang;
                        }
                    }
                }
            } else if ($data->kantor_pegawai == 'Kantor Wilayah') {

                $unit_kerja = DB::table('tb_kepala_unit_kerja')
                    ->where([['tb_kepala_unit_kerja.id_pegawai', $data->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N'], ['tb_kepala_unit_kerja.kantor_pegawai', $data->kantor_pegawai]])
                    ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
                    ->limit(1)
                    ->get();

                if ($unit_kerja->count() > 0) {
                    foreach ($unit_kerja as $data_unit_kerja) {

                        $kantor_bagian = DB::table('tb_bagian_kantor_wilayah')
                            ->join('tb_kantor_wilayah', 'tb_kantor_wilayah.id_kantor_wilayah', '=', 'tb_bagian_kantor_wilayah.id_kantor_wilayah')
                            ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data_unit_kerja->id_bagian_kantor_wilayah)
                            ->get();
                        if ($kantor_bagian->count() > 0) {
                            foreach ($kantor_bagian as $data_kantor_bagian) {
                                $kantor = $data_kantor_bagian->nama_kantor_wilayah;
                                $bagian = 'Semua Bagian';
                            }
                        }
                    }
                } else {

                    $kantor_bagian = DB::table('tb_bagian_kantor_wilayah')
                        ->join('tb_kantor_wilayah', 'tb_kantor_wilayah.id_kantor_wilayah', '=', 'tb_bagian_kantor_wilayah.id_kantor_wilayah')
                        ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data->id_bagian_kantor_wilayah)
                        ->get();
                    if ($kantor_bagian->count() > 0) {
                        foreach ($kantor_bagian as $data_kantor_bagian) {
                            $kantor = $data_kantor_bagian->nama_kantor_wilayah;
                            $bagian = $data_kantor_bagian->nama_bagian_kantor_wilayah;
                        }
                    }
                }
            }

            $delete = "delete_data(" . $data->id_pegawai . ", '" . $data->nama_pegawai . "')";

            $update_data = $role->can_update == "Y" ? '<a href="?update=' . $data->id_pegawai . '"><span class="badge badge-primary"><i class="bx bx-edit"></i> Ubah</span></a>' : "-";
            $delete_data = $role->can_delete == "Y" ? '<a href="javascript:;" onclick="' . $delete . '"><span class="badge badge-danger"><i class="bx bx-trash"></i> Hapus</span></a>' : "-";

            $data->no = $no++;
            $data->npp_pegawai = $data->npp_pegawai;
            $data->nama_pegawai = '
				<a href="?view=' . $data->id_pegawai . '" class="text-info">
					<img src="' . asset($data->foto_pegawai) . '" style="width: 20px;height: 20px;border-radius: 100%;"> 
					' . $data->nama_pegawai . '
				</a>
			';
            $data->telp_pegawai = $data->telp_pegawai;
            $data->email_pegawai = $data->email_pegawai;
            $data->kantor_pegawai = $data->kantor_pegawai;
            $data->bagian = $kantor . ' - ' . $bagian;
            $data->level_pegawai = $data->level_pegawai;
            $data->status_pegawai = $data->status_pegawai;
            $data->tgl_pegawai = date('j F Y, H:i', strtotime($data->tgl_pegawai));
            $data->action = $update_data . "&nbsp;" . $delete_data;
        }

        return DataTables::of($pelanggan)->escapecolumns([])->make(true);
    }

    public function log(Request $request)
    {
        $log = DB::table('tb_log_pegawai')
            ->join('tb_pegawai', 'tb_pegawai.id_pegawai', '=', 'tb_log_pegawai.id_pegawai')
            ->where([['tb_log_pegawai.delete_log_pegawai', 'N'], ['tb_log_pegawai.id_pegawai', $_GET['view']]])
            ->orderBy('tb_log_pegawai.id_log_pegawai', 'DESC')
            ->get();

        $no = 1;
        foreach ($log as $data) {

            $data->no = $no++;
            $data->nama_pegawai = $data->nama_pegawai;
            $data->tgl_log_pegawai = date('j F Y, H:i', strtotime($data->tgl_log_pegawai));
        }

        return DataTables::of($log)->escapecolumns([])->make(true);
    }

    public function save(Request $request)
    {
        $pegawai = DB::table('tb_pegawai')
            ->where([['tb_pegawai.id_pegawai', Auth::user()->id_pegawai], ['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif']])
            ->get();

        if ($pegawai->count() < 1) {
            return back();
        } else {

            foreach ($pegawai as $data_pegawai) {

                $kantor = $request->kantor;
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
                $npp = preg_replace('/\D/', '', $request->npp);
                $nama = $request->nama;
                $jenkel = $request->jenkel;
                $telp = preg_replace('/\D/', '', $request->telp);
                $email = $request->email;
                $foto = url('logos/avatar.png');
                if (!empty($request->file('foto'))) {
                    $file_foto = 'foto_pelanggan_' . date('Ymd_His.') . $request->file('foto')->getClientOriginalExtension();
                    $image_resize = Image::make($request->file('foto')->getRealPath());
                    $image_resize->fit(250);
                    $image_resize->save(public_path('../images/' . $file_foto));
                    $foto = url('images/' . $file_foto);
                }
                $password = md5($request->password);
                $level = $request->level;
                $status = $request->status;
                $sebagai = 'Mitra/Pelanggan';
                $unit_kerja = $request->unit_kerja;
                $id_unit_kerja = explode('_', $unit_kerja);
                $tgl = date('Y-m-d H:i:s');

                $pelanggan = DB::table('tb_pegawai')
                    ->where('tb_pegawai.delete_pegawai', '=', 'N')
                    ->where('tb_pegawai.status_pegawai', '=', 'Aktif')
                    ->where('tb_pegawai.sebagai_pegawai', '=', 'Mitra/Pelanggan')
                    ->where(function ($query) use ($npp, $telp, $email) {
                        $query->where('tb_pegawai.npp_pegawai', '=', $npp)
                            ->orWhere('tb_pegawai.telp_pegawai', '=', $telp)
                            ->orWhere('tb_pegawai.email_pegawai', '=', $email);
                    })
                    ->get();

                if ($pelanggan->count() < 1) {

                    if ($request->bagian == 'all') {

                        $values = array(
                            'kantor_pegawai' => $kantor,
                            'id_bagian_kantor_pusat' => 0,
                            'id_bagian_kantor_cabang' => 0,
                            'id_bagian_kantor_wilayah' => 0,
                            'npp_pegawai' => $npp,
                            'nama_pegawai' => $nama,
                            'jenkel_pegawai' => $jenkel,
                            'telp_pegawai' => $telp,
                            'email_pegawai' => $email,
                            'foto_pegawai' => $foto,
                            'password_pegawai' => $password,
                            'level_pegawai' => $level,
                            'status_pegawai' => $status,
                            'sebagai_pegawai' => $sebagai,
                            'multi_pegawai' => $id_unit_kerja[2],
                            'tgl_pegawai' => $tgl,
                            'created_by' => $data_pegawai->nama_pegawai,
                            'created_date' => date('Y-m-d H:i:s'),
                        );
                        DB::table('tb_pegawai')->insert($values);

                        $new_pelanggan = DB::table('tb_pegawai')
                            ->where([['tb_pegawai.npp_pegawai', $npp], ['tb_pegawai.email_pegawai', $email], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.sebagai_pegawai', $sebagai], ['tb_pegawai.delete_pegawai', 'N']])
                            ->orderBy('tb_pegawai.id_pegawai', 'DESC')
                            ->limit(1)
                            ->get();

                        if ($new_pelanggan->count() > 0) {

                            foreach ($new_pelanggan as $data_new_pelanggan) {

                                if ($data_new_pelanggan->kantor_pegawai == 'Kantor Pusat') {

                                    $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
                                        ->join('tb_kantor_pusat', 'tb_kantor_pusat.id_kantor_pusat', '=', 'tb_bagian_kantor_pusat.id_kantor_pusat')
                                        ->where([['tb_bagian_kantor_pusat.delete_bagian_kantor_pusat', 'N'], ['tb_bagian_kantor_pusat.id_kantor_pusat', $id_unit_kerja[2]]])
                                        ->orderBy('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', 'ASC')
                                        ->get();

                                    if ($kantor_pusat->count() > 0) {

                                        foreach ($kantor_pusat as $data_kantor_pusat) {

                                            $values = array(
                                                'id_pegawai' => $data_new_pelanggan->id_pegawai,
                                                'kantor_pegawai' => $data_new_pelanggan->kantor_pegawai,
                                                'id_bagian_kantor_pusat' => $data_kantor_pusat->id_bagian_kantor_pusat,
                                                'id_bagian_kantor_cabang' => 0,
                                                'id_bagian_kantor_wilayah' => 0,
                                            );
                                            DB::table('tb_kepala_unit_kerja')->insert($values);
                                        }
                                    }
                                } else if ($data_new_pelanggan->kantor_pegawai == 'Kantor Cabang') {

                                    $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
                                        ->join('tb_kantor_cabang', 'tb_kantor_cabang.id_kantor_cabang', '=', 'tb_bagian_kantor_cabang.id_kantor_cabang')
                                        ->where([['tb_bagian_kantor_cabang.delete_bagian_kantor_cabang', 'N'], ['tb_bagian_kantor_cabang.id_kantor_cabang', $id_unit_kerja[2]]])
                                        ->orderBy('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', 'ASC')
                                        ->get();

                                    if ($kantor_cabang->count() > 0) {

                                        foreach ($kantor_cabang as $data_kantor_cabang) {

                                            $values = array(
                                                'id_pegawai' => $data_new_pelanggan->id_pegawai,
                                                'kantor_pegawai' => $data_new_pelanggan->kantor_pegawai,
                                                'id_bagian_kantor_cabang' => $data_kantor_cabang->id_bagian_kantor_cabang,
                                                'id_bagian_kantor_pusat' => 0,
                                                'id_bagian_kantor_wilayah' => 0,
                                            );
                                            DB::table('tb_kepala_unit_kerja')->insert($values);
                                        }
                                    }
                                } else if ($data_new_pelanggan->kantor_pegawai == 'Kantor Wilayah') {

                                    $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
                                        ->join('tb_kantor_wilayah', 'tb_kantor_wilayah.id_kantor_wilayah', '=', 'tb_bagian_kantor_wilayah.id_kantor_wilayah')
                                        ->where([['tb_bagian_kantor_wilayah.delete_bagian_kantor_wilayah', 'N'], ['tb_bagian_kantor_wilayah.id_kantor_wilayah', $id_unit_kerja[2]]])
                                        ->orderBy('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', 'ASC')
                                        ->get();

                                    if ($kantor_wilayah->count() > 0) {

                                        foreach ($kantor_wilayah as $data_kantor_wilayah) {

                                            $values = array(
                                                'id_pegawai' => $data_new_pelanggan->id_pegawai,
                                                'kantor_pegawai' => $data_new_pelanggan->kantor_pegawai,
                                                'id_bagian_kantor_wilayah' => $data_kantor_wilayah->id_bagian_kantor_wilayah,
                                                'id_bagian_kantor_pusat' => 0,
                                                'id_bagian_kantor_cabang' => 0,
                                            );
                                            DB::table('tb_kepala_unit_kerja')->insert($values);
                                        }
                                    }
                                }
                            }
                        }
                    } else {

                        $values = array(
                            'kantor_pegawai' => $kantor,
                            'id_bagian_kantor_pusat' => $bagian_kantor_pusat,
                            'id_bagian_kantor_cabang' => $bagian_kantor_cabang,
                            'id_bagian_kantor_wilayah' => $bagian_kantor_wilayah,
                            'npp_pegawai' => $npp,
                            'nama_pegawai' => $nama,
                            'jenkel_pegawai' => $jenkel,
                            'telp_pegawai' => $telp,
                            'email_pegawai' => $email,
                            'foto_pegawai' => $foto,
                            'password_pegawai' => $password,
                            'level_pegawai' => $level,
                            'status_pegawai' => $status,
                            'sebagai_pegawai' => $sebagai,
                            'multi_pegawai' => 'Tidak',
                            'tgl_pegawai' => $tgl,
                            'created_by' => $data_pegawai->nama_pegawai,
                            'created_date' => date('Y-m-d H:i:s'),
                        );
                        DB::table('tb_pegawai')->insert($values);
                    }

                    return redirect()->route('pelanggan');
                } else {

                    foreach ($pelanggan as $data_pelanggan);

                    if ($data_pelanggan->npp_pegawai == $npp) {
                        return back()->with('alert', 'danger_NPP sudah terdaftar sebelumnya.')->withInput($request->all());
                    } else if ($data_pelanggan->telp_pegawai == $telp) {
                        return back()->with('alert', 'danger_No.Telp sudah terdaftar sebelumnya.')->withInput($request->all());
                    } else if ($data_pelanggan->email_pegawai == $email) {
                        return back()->with('alert', 'danger_Email sudah terdaftar sebelumnya.')->withInput($request->all());
                    }
                }
            }
        }
    }

    public function update(Request $request)
    {
        $pegawai = DB::table('tb_pegawai')
            ->where([['tb_pegawai.id_pegawai', Auth::user()->id_pegawai], ['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif']])
            ->get();

        if ($pegawai->count() < 1) {
            return back();
        } else {

            foreach ($pegawai as $data_pegawai) {

                $id = $request->update;

                $get_pelanggan = DB::table('tb_pegawai')
                    ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.sebagai_pegawai', 'Mitra/Pelanggan'], ['tb_pegawai.id_pegawai', $id]])
                    ->get();

                if ($get_pelanggan->count() < 1) {
                    return back();
                } else {

                    foreach ($get_pelanggan as $data_get_pelanggan);

                    $kantor = $request->kantor;
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
                    $npp = preg_replace('/\D/', '', $request->npp);
                    $nama = $request->nama;
                    $jenkel = $request->jenkel;
                    $telp = preg_replace('/\D/', '', $request->telp);
                    $email = $request->email;
                    $foto = $data_get_pelanggan->foto_pegawai;
                    if (!empty($request->file('foto'))) {
                        $file_foto = 'foto_pelanggan_' . date('Ymd_His.') . $request->file('foto')->getClientOriginalExtension();
                        $image_resize = Image::make($request->file('foto')->getRealPath());
                        $image_resize->fit(250);
                        $image_resize->save(public_path('../images/' . $file_foto));
                        $foto = url('images/' . $file_foto);
                    }
                    $password = $data_get_pelanggan->password_pegawai;
                    if (!empty($request->password)) {
                        $password = md5($request->password);
                    }
                    $level = $request->level;
                    $status = $request->status;
                    $unit_kerja = $request->unit_kerja;
                    $id_unit_kerja = explode('_', $unit_kerja);

                    $pelanggan = DB::table('tb_pegawai')
                        ->where('tb_pegawai.id_pegawai', '!=', $data_get_pelanggan->id_pegawai)
                        ->where('tb_pegawai.delete_pegawai', '=', 'N')
                        ->where('tb_pegawai.status_pegawai', '=', 'Aktif')
                        ->where('tb_pegawai.sebagai_pegawai', '=', 'Mitra/Pelanggan')
                        ->where(function ($query) use ($npp, $telp, $email) {
                            $query->where('tb_pegawai.npp_pegawai', '=', $npp)
                                ->orWhere('tb_pegawai.telp_pegawai', '=', $telp)
                                ->orWhere('tb_pegawai.email_pegawai', '=', $email);
                        })
                        ->get();

                    if ($pelanggan->count() < 1) {

                        if ($request->bagian == 'all') {

                            $where = array(
                                'id_pegawai' => $id,
                                'delete_pegawai' => 'N',
                            );
                            $values = array(
                                'kantor_pegawai' => $kantor,
                                'id_bagian_kantor_pusat' => 0,
                                'id_bagian_kantor_cabang' => 0,
                                'id_bagian_kantor_wilayah' => 0,
                                'npp_pegawai' => $npp,
                                'nama_pegawai' => $nama,
                                'jenkel_pegawai' => $jenkel,
                                'telp_pegawai' => $telp,
                                'email_pegawai' => $email,
                                'foto_pegawai' => $foto,
                                'password_pegawai' => $password,
                                'level_pegawai' => $level,
                                'status_pegawai' => $status,
                                'multi_pegawai' => $id_unit_kerja[2],
                                'update_by' => $data_pegawai->nama_pegawai,
                                'update_date' => date('Y-m-d H:i:s'),
                            );
                            DB::table('tb_pegawai')->where($where)->update($values);

                            // delete kepala unit kerja
                            $where = array(
                                'id_pegawai' => $id,
                                'delete_kepala_unit_kerja' => 'N',
                            );
                            $values = array(
                                'delete_kepala_unit_kerja' => 'Y',
                            );
                            DB::table('tb_kepala_unit_kerja')->where($where)->update($values);
                            // end delete kepala unit kerja

                            // insert new kepala unit kerja
                            if ($kantor == 'Kantor Pusat') {

                                $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
                                    ->join('tb_kantor_pusat', 'tb_kantor_pusat.id_kantor_pusat', '=', 'tb_bagian_kantor_pusat.id_kantor_pusat')
                                    ->where([['tb_bagian_kantor_pusat.delete_bagian_kantor_pusat', 'N'], ['tb_bagian_kantor_pusat.id_kantor_pusat', $id_unit_kerja[2]]])
                                    ->orderBy('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', 'ASC')
                                    ->get();

                                if ($kantor_pusat->count() > 0) {

                                    foreach ($kantor_pusat as $data_kantor_pusat) {

                                        $values = array(
                                            'id_pegawai' => $id,
                                            'kantor_pegawai' => $kantor,
                                            'id_bagian_kantor_pusat' => $data_kantor_pusat->id_bagian_kantor_pusat,
                                            'id_bagian_kantor_cabang' => 0,
                                            'id_bagian_kantor_wilayah' => 0,
                                        );
                                        DB::table('tb_kepala_unit_kerja')->insert($values);
                                    }
                                }
                            } else if ($kantor == 'Kantor Cabang') {

                                $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
                                    ->join('tb_kantor_cabang', 'tb_kantor_cabang.id_kantor_cabang', '=', 'tb_bagian_kantor_cabang.id_kantor_cabang')
                                    ->where([['tb_bagian_kantor_cabang.delete_bagian_kantor_cabang', 'N'], ['tb_bagian_kantor_cabang.id_kantor_cabang', $id_unit_kerja[2]]])
                                    ->orderBy('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', 'ASC')
                                    ->get();

                                if ($kantor_cabang->count() > 0) {

                                    foreach ($kantor_cabang as $data_kantor_cabang) {

                                        $values = array(
                                            'id_pegawai' => $id,
                                            'kantor_pegawai' => $kantor,
                                            'id_bagian_kantor_cabang' => $data_kantor_cabang->id_bagian_kantor_cabang,
                                            'id_bagian_kantor_pusat' => 0,
                                            'id_bagian_kantor_wilayah' => 0,
                                        );
                                        DB::table('tb_kepala_unit_kerja')->insert($values);
                                    }
                                }
                            } else if ($kantor == 'Kantor Wilayah') {

                                $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
                                    ->join('tb_kantor_wilayah', 'tb_kantor_wilayah.id_kantor_wilayah', '=', 'tb_bagian_kantor_wilayah.id_kantor_wilayah')
                                    ->where([['tb_bagian_kantor_wilayah.delete_bagian_kantor_wilayah', 'N'], ['tb_bagian_kantor_wilayah.id_kantor_wilayah', $id_unit_kerja[2]]])
                                    ->orderBy('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', 'ASC')
                                    ->get();

                                if ($kantor_wilayah->count() > 0) {

                                    foreach ($kantor_wilayah as $data_kantor_wilayah) {

                                        $values = array(
                                            'id_pegawai' => $id,
                                            'kantor_pegawai' => $kantor,
                                            'id_bagian_kantor_wilayah' => $data_kantor_wilayah->id_bagian_kantor_wilayah,
                                            'id_bagian_kantor_pusat' => 0,
                                            'id_bagian_kantor_cabang' => 0,
                                        );
                                        DB::table('tb_kepala_unit_kerja')->insert($values);
                                    }
                                }
                            }

                            // end insert new kepala unit kerja

                        } else {

                            $where = array(
                                'id_pegawai' => $id,
                                'delete_pegawai' => 'N',
                            );
                            $values = array(
                                'kantor_pegawai' => $kantor,
                                'id_bagian_kantor_pusat' => $bagian_kantor_pusat,
                                'id_bagian_kantor_cabang' => $bagian_kantor_cabang,
                                'id_bagian_kantor_wilayah' => $bagian_kantor_wilayah,
                                'npp_pegawai' => $npp,
                                'nama_pegawai' => $nama,
                                'jenkel_pegawai' => $jenkel,
                                'telp_pegawai' => $telp,
                                'email_pegawai' => $email,
                                'foto_pegawai' => $foto,
                                'password_pegawai' => $password,
                                'level_pegawai' => $level,
                                'status_pegawai' => $status,
                                'multi_pegawai' => 'Tidak',
                                'update_by' => $data_pegawai->nama_pegawai,
                                'update_date' => date('Y-m-d H:i:s'),
                            );
                            DB::table('tb_pegawai')->where($where)->update($values);
                        }

                        return back()->with('alert', 'success_Berhasil diperbarui.');
                    } else {

                        foreach ($pelanggan as $data_pelanggan);

                        if ($data_pelanggan->npp_pegawai == $npp) {
                            return back()->with('alert', 'danger_NPP sudah terdaftar sebelumnya.')->withInput($request->all());
                        } else if ($data_pelanggan->telp_pegawai == $telp) {
                            return back()->with('alert', 'danger_No.Telp sudah terdaftar sebelumnya.')->withInput($request->all());
                        } else if ($data_pelanggan->email_pegawai == $email) {
                            return back()->with('alert', 'danger_Email sudah terdaftar sebelumnya.')->withInput($request->all());
                        }
                    }
                }
            }
        }
    }

    public function delete(Request $request)
    {
        $pegawai = DB::table('tb_pegawai')
            ->where([['tb_pegawai.id_pegawai', Auth::user()->id_pegawai], ['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif']])
            ->get();

        if ($pegawai->count() < 1) {
            return back();
        } else {

            foreach ($pegawai as $data_pegawai) {

                $id = $request->delete;
                $where = array(
                    'id_pegawai' => $id,
                    'delete_pegawai' => 'N',
                );
                $values = array(
                    'delete_pegawai' => 'Y',
                    'delete_by' => $data_pegawai->nama_pegawai,
                    'delete_date' => date('Y-m-d H:i:s'),
                );
                DB::table('tb_pegawai')->where($where)->update($values);
                return back();
            }
        }
    }
}
