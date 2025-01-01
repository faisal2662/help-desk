<?php

namespace App\Http\Controllers;

use Mail;
use Session;
use Socialite;
use DataTables;
use Nette\Utils\Image;
use App\Models\Pegawai;
use App\Models\KepalaUnit;
use App\Models\NamaPosisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
            $route = route('pelanggan.create');
            $input = "<a href='$route'>
								<span class='badge badge-primary'>
								  <i class='bx bx-plus'></i> Tambah User
								</span>
							</a>";
        } else {
            $input = "";
        }

        // $pegawaiList = DB::table('tb_pegawai')->get();
        // $id= [];
        // foreach ($pegawaiList as $pegawai) {


        //     // Hapus bagian seperti 'KC Denpasar' dari section_name pegawai
        //     $sectionName = preg_replace('/KW .*/', '', $pegawai->section_name);
        //     $sectionName = trim($sectionName);
        //     // $sectionName= 'Bagian '.$sectionName;
        //     // $sectionName= $pegawai->section_name;


        //     // Ambil semua nama kantor cabang
        //     $kantorCabangList = DB::table('tb_kantor_wilayah')->pluck('id_kantor_wilayah', 'nama_kantor_wilayah')->toArray();


        //     // Periksa apakah branch_name pegawai ada dalam daftar nama kantor cabang
        //     // if($pegawai->branch_name == 'Satuan Pengawasan Intern ') {
        //         // }
        //         if (array_key_exists($pegawai->branch_name, $kantorCabangList)) {
        //             // dd($kantorCabangList);
        //             // dd($pegawai);
        //         $idKantorCabang = $kantorCabangList[$pegawai->branch_name];
        //         // Cari bagian kantor cabang berdasarkan ID kantor cabang dan section_name
        //         $bagianKantorCabang = DB::table('tb_bagian_kantor_wilayah')
        //         ->where('id_kantor_wilayah', $idKantorCabang)
        //         ->where('nama_bagian_kantor_wilayah', $sectionName)
        //         ->first();
        //         // dd($bagianKantorCabang);
        //         // dd($sectionName);
        //         // echo $pegawai;
        //         if ($bagianKantorCabang) {
        //             // dd($pegawai);

        //             // Update ID bagian kantor cabang pada tabel pegawai
        //             DB::table('tb_pegawai')
        //             ->where('id_pegawai', $pegawai->id_pegawai)
        //             ->update(['id_bagian_kantor_wilayah' => $bagianKantorCabang->id_bagian_kantor_wilayah, 'kantor_pegawai' => 'Kantor Wilayah']);
        //             // dd($pegawai->id_pegawai);
        //             $id[]= ['id_pegawai'=> $pegawai->id_pegawai,
        //             'Nama Pegawai' => $pegawai->employee_name];
        //         }
        //     }
        //     // dd('tidak');
        // }
        // dd($id);
        // dd('tidak');

        return view('pages.user.index', compact('input'));
    }
    private function decryptssl($str, $key)
    {
        $str = base64_decode($str);
        $key = base64_decode($key);
        $decrypted = openssl_decrypt($str, 'AES-128-ECB', $key,  OPENSSL_RAW_DATA);
        return $decrypted;
    }
    public function getApi()
    {
        $filePath = base_path('../data/data_full.json');
        $data_api = null;

        if (File::exists($filePath)) {
            // Membaca konten file JSON
            $jsonContent = File::get($filePath);

            // Mengonversi konten JSON menjadi array
            $data_api = json_decode($jsonContent, true);

            $data = [];

            // Ambil semua posisi jabatan dari database (mapping nama ke ID)
            $posisiMapping = NamaPosisi::where('is_delete', 'N')->pluck('id_posisi_pegawai', 'nama_posisi')->toArray();

            foreach ($data_api['data'] as $pegawai) {
                // Cek apakah nama jabatan ada di mapping
                if (array_key_exists($pegawai['position_name'], $posisiMapping)) {
                    $posisiId = $posisiMapping[$pegawai['position_name']];

                    // Masukkan data pegawai ke array (atau langsung ke database)
                    DB::table('tb_pegawai_new')->insert($pegawai);
                    $data[] = [
                        'nama' => $pegawai['employee_name'], // Sesuaikan dengan key JSON Anda
                        'id' => $pegawai['employee_id'], // Sesuaikan dengan key JSON Anda
                        'email' => $pegawai['email'], // Sesuaikan dengan key JSON Anda
                        'id_posisi_jabatan' => $posisiId,
                    ];
                } else {
                    // Log jika nama jabatan tidak ditemukan di database
                    Log::warning('Nama jabatan tidak ditemukan: ' . $pegawai['position_name']);
                }
            }

            // Simpan semua data ke database dalam satu query (opsional)
            // Pegawai::insert($data);

            // Debug hasil data
            dd($data);
        }



        // Menampilkan data (misalnya)
        // return response()->json($data);
        else {
            dd('tidak ditemukan');
            return response()->json(['error' => 'File tidak ditemukan'], 404);
        }
    }




    public function datatables(Request $request)
    {
        $role = $this->role->role(Auth::user()->id_pegawai, "", $this->route);

        $pelanggan = Pegawai::with('NamaPosisi')
            ->where([['tb_pegawai.delete_pegawai', 'N']])
            ->orderBy('tb_pegawai.id_pegawai', 'DESC')
            ->get();
        // $filePath = base_path('../data/data_full.json');

        // $jsonContent = File::get($filePath);

        //         // Mengonversi konten JSON menjadi array
        // $pelanggan = json_decode($jsonContent, true);
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

                        $kantor_bagian = DB::table('tb_kantor_pusat')
                            ->where('tb_kantor_pusat.id_kantor_pusat', '=', $data_unit_kerja->id_bagian_kantor_pusat)
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

                        $kantor_bagian = DB::table('tb_kantor_cabang')
                            ->where('tb_kantor_cabang.id_kantor_cabang', '=', $data_unit_kerja->id_bagian_kantor_cabang)
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

                        $kantor_bagian = DB::table('tb_kantor_wilayah')
                            ->where('tb_kantor_wilayah.id_kantor_wilayah', '=', $data_unit_kerja->id_bagian_kantor_wilayah)
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

            $delete = "delete_data(" . $data->id_pegawai . ", '" . $data->employee_name . "')";

            $update_data = $role->can_update == "Y" ? '<a href="' . route('pelanggan.edit', $data->id_pegawai) . '"><span class="badge badge-primary"><i class="bx bx-edit"></i> Ubah</span></a>' : "-";
            $delete_data = $role->can_delete == "Y" ? '<a href="javascript:;" onclick="' . $delete . '"><span class="badge badge-danger"><i class="bx bx-trash"></i> Hapus</span></a>' : "-";

            $data->no = $no++;
            $data->npp_pegawai = $data->employee_id;
            if ($data->foto_pegawai) {

                $data->nama_pegawai = '
                    <a href="' . route('pelanggan.show', $data->id_pegawai) . '" class="text-info">
                        <img src="' . asset($data->foto_pegawai) . '" style="width: 20px;height: 20px;border-radius: 100%;">
                        ' . $data->employee_name . '
                    </a>
                ';
            } else {

                $data->nama_pegawai = '
				<a href="' . route('pelanggan.show', $data->id_pegawai) . '" class="text-info">
                <img src="' . asset('logos/avatar.png') . '" style="width: 20px;height: 20px;border-radius: 100%;">
                ' . $data->employee_name . '
				</a>
                ';
            }
            if ($data->status_data == 'local') {
                $data->telp_pegawai =  $data->primary_phone;
                $data->email_pegawai = $data->email;
            } else {
                $data->telp_pegawai =  $this->decryptssl($data->primary_phone, 'P/zqOYfEDWHmQ9/g8PrApw==');
                $data->email_pegawai = $this->decryptssl($data->email, 'P/zqOYfEDWHmQ9/g8PrApw==');
            }



            $data->kantor_pegawai = $data->branch_name;
            $data->bagian = $data->department_name;
            $data->level_pegawai = $data->NamaPosisi ? $data->NamaPosisi->sebagai_posisi : ' ';
            $data->status_pegawai = $data->status_pegawai;
            $data->tgl_pegawai = date('j F Y, H:i', strtotime($data->created_date));
            $data->action = $update_data . "&nbsp;" . $delete_data;
        }

        return DataTables::of($pelanggan)->escapecolumns([])->make(true);
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

        return view('pages.user.tambah', compact('unit_kerja', 'kantor_pusat', 'kantor_cabang', 'kantor_wilayah', 'bagian_kantor_pusat', 'bagian_kantor_cabang', 'bagian_kantor_wilayah'));
    }
    public function edit($id)
    {
        $pegawai = Pegawai::with('NamaPosisi')->where('id_pegawai', $id)->where('delete_pegawai', 'N')->first();
        $nama_posisi = NamaPosisi::where('is_delete', 'N')->get();
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
        $kepala_unit_kerja = KepalaUnit::where('id_pegawai', $id)->where('delete_kepala_unit_kerja', 'N')->first();

        if (!is_null($kepala_unit_kerja)) {
            $id_unit_kerja = '';
            $bagian_unit = '';
            $id_bagian = '';
            if ($kepala_unit_kerja->id_bagian_kantor_pusat != 0) {
                $bagian =  $kantor_pusat->where('id_kantor_pusat', $kepala_unit_kerja->id_bagian_kantor_pusat)->first();
                $id_unit_kerja = 'Pusat,' . $bagian->id_kantor_pusat;
                $bagian_unit = 'Semua Bagian';
                $id_bagian = '0';
            } elseif ($kepala_unit_kerja->id_bagian_kantor_cabang != 0) {
                $bagian = $kantor_cabang->where('id_kantor_cabang', $kepala_unit_kerja->id_bagian_kantor_cabang)->first();
                $id_unit_kerja = 'Cabang,' .  $bagian->id_kantor_cabang;
                $bagian_unit = 'Semua Bagian';
                $id_bagian = '0';
            }
            if ($kepala_unit_kerja->id_bagian_kantor_wilayah != 0) {
                $bagian = $kantor_wilayah->where('id_kantor_wilayah', $kepala_unit_kerja->id_bagian_kantor_wilayah)->first();
                $id_unit_kerja = 'Wilayah,' .  $bagian->id_kantor_wilayah;
                $bagian_unit = 'Semua Bagian';
                $id_bagian = '0';
            }

            return view('pages.user.ubah', compact('unit_kerja','nama_posisi', 'pegawai', 'id_unit_kerja', 'bagian_unit', 'id_bagian', 'kantor_pusat', 'kantor_cabang', 'kantor_wilayah', 'bagian_kantor_pusat', 'bagian_kantor_cabang', 'bagian_kantor_wilayah'));
        }
        $id_unit_kerja = '';
        $bagian_unit = '';
        $id_bagian = '';
        if ($pegawai->id_bagian_kantor_pusat != 0) {
            $bagian =  $bagian_kantor_pusat->where('id_bagian_kantor_pusat', $pegawai->id_bagian_kantor_pusat)->first();
            if (!is_null($bagian)) {
                $id_unit_kerja = 'Pusat,' . $bagian->id_kantor_pusat;
                $bagian_unit = $bagian->nama_bagian_kantor_pusat;
                $id_bagian = $bagian->nama_bagian_kantor_pusat;
            }
        } elseif ($pegawai->id_bagian_kantor_cabang != 0) {
            $bagian = $bagian_kantor_cabang->where('id_bagian_kantor_cabang', $pegawai->id_bagian_kantor_cabang)->first();
            if (!is_null($bagian)) {

                $id_unit_kerja = 'Cabang,' .  $bagian->id_kantor_cabang;
                $bagian_unit = $bagian->nama_bagian_kantor_cabang;
                $id_bagian = $bagian->id_bagian_kantor_cabang;
            }
        } elseif ($pegawai->id_bagian_kantor_wilayah != 0) {
            $bagian = $bagian_kantor_wilayah->where('id_bagian_kantor_wilayah', $pegawai->id_bagian_kantor_wilayah)->first();
            if (!is_null($bagian)) {

                $id_unit_kerja = 'Wilayah,' .  $bagian->id_kantor_wilayah;
                $bagian_unit = $bagian->nama_bagian_kantor_wilayah;
                $id_bagian = $bagian->id_bagian_kantor_wilayah;
            }
        }

        if ($pegawai->status_data == 'local') {
            $pegawai->primary_phone = $pegawai->primary_phone;
            $pegawai->email = $pegawai->email;
        } else {
            $pegawai->primary_phone =  $this->decryptssl($pegawai->primary_phone, 'P/zqOYfEDWHmQ9/g8PrApw==');
            $pegawai->email = $this->decryptssl($pegawai->email, 'P/zqOYfEDWHmQ9/g8PrApw==');
        }
        return view('pages.user.ubah', compact('unit_kerja', 'pegawai','nama_posisi', 'id_unit_kerja', 'bagian_unit', 'id_bagian', 'kantor_pusat', 'kantor_cabang', 'kantor_wilayah', 'bagian_kantor_pusat', 'bagian_kantor_cabang', 'bagian_kantor_wilayah'));
    }

    public function get_posisi( Request $request){
            $nama_posisi = NamaPosisi::where('is_delete', 'N')->where('id_posisi_pegawai', $request->id)->first();
        return response()->json($nama_posisi);

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
                $kantor = explode(',', $request->kantor);
                $id_kantor = $kantor[1];
                $kantor = $kantor[0];
                $kantor = 'Kantor ' . $kantor;

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
                    // $image_resize = Image::make($request->file('foto')->getRealPath());
                    // $image_resize->fit(250);
                    // $image_resize->save(public_path('../images/' . $file_foto));
                    $file  = $request->file('foto');
                    $file->move(base_path('../images/'),  $file_foto);
                    $foto = url('images/' . $file_foto);
                }
                $password = md5($request->password);
                $level = $request->level;
                $status = $request->status;
                $sebagai = $request->sebagai;
                $unit_kerja = $request->unit_kerja;
                $id_unit_kerja = explode('_', $unit_kerja);
                $tgl = date('Y-m-d H:i:s');
                $pelanggan = DB::table('tb_pegawai')
                    ->where('tb_pegawai.delete_pegawai', '=', 'N')
                    ->where('tb_pegawai.status_pegawai', '=', 'Aktif')
                    ->where(function ($query) use ($npp, $telp, $email) {
                        $query->where('tb_pegawai.npp_pegawai', '=', $npp)
                            ->orWhere('tb_pegawai.telp_pegawai', '=', $telp)
                            ->orWhere('tb_pegawai.email_pegawai', '=', $email);
                    })
                    ->get();


                if ($pelanggan->count() < 1) {


                    if ($request->bagian == 0) {

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


                                if ($data_new_pelanggan->kantor_pegawai === 'Kantor Pusat') {

                                    $kantor_pusat = DB::table('tb_kantor_pusat')
                                        ->where([['tb_kantor_pusat.delete_kantor_pusat', 'N'], ['tb_kantor_pusat.id_kantor_pusat', $id_kantor]])
                                        ->orderBy('tb_kantor_pusat.id_kantor_pusat', 'ASC')
                                        ->get();

                                    if ($kantor_pusat->count() > 0) {

                                        foreach ($kantor_pusat as $data_kantor_pusat) {

                                            $values = array(
                                                'id_pegawai' => $data_new_pelanggan->id_pegawai,
                                                'kantor_pegawai' => $data_new_pelanggan->kantor_pegawai,
                                                'id_bagian_kantor_pusat' => $data_kantor_pusat->id_kantor_pusat,
                                                'id_bagian_kantor_cabang' => 0,
                                                'id_bagian_kantor_wilayah' => 0,
                                            );

                                            DB::table('tb_kepala_unit_kerja')->insert($values);
                                        }
                                    }
                                } else if ($data_new_pelanggan->kantor_pegawai === 'Kantor Cabang') {

                                    $kantor_cabang = DB::table('tb_kantor_cabang')
                                        ->where([['tb_kantor_cabang.delete_kantor_cabang', 'N'], ['tb_kantor_cabang.id_kantor_cabang', $id_kantor]])
                                        ->orderBy('tb_kantor_cabang.id_kantor_cabang', 'ASC')
                                        ->get();

                                    if ($kantor_cabang->count() > 0) {

                                        foreach ($kantor_cabang as $data_kantor_cabang) {

                                            $values = array(
                                                'id_pegawai' => $data_new_pelanggan->id_pegawai,
                                                'kantor_pegawai' => $data_new_pelanggan->kantor_pegawai,
                                                'id_bagian_kantor_cabang' => $data_kantor_cabang->id_kantor_cabang,
                                                'id_bagian_kantor_pusat' => 0,
                                                'id_bagian_kantor_wilayah' => 0,
                                            );
                                            DB::table('tb_kepala_unit_kerja')->insert($values);
                                        }
                                    }
                                } else if ($data_new_pelanggan->kantor_pegawai === 'Kantor Wilayah') {

                                    $kantor_wilayah = DB::table('tb_kantor_wilayah')
                                        ->join('tb_kantor_wilayah', 'tb_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
                                        ->where([['tb_kantor_wilayah.delete_kantor_wilayah', 'N'], ['tb_kantor_wilayah.id_kantor_wilayah', $id_kantor]])
                                        ->orderBy('tb_kantor_wilayah.id_kantor_wilayah', 'ASC')
                                        ->get();

                                    if ($kantor_wilayah->count() > 0) {

                                        foreach ($kantor_wilayah as $data_kantor_wilayah) {

                                            $values = array(
                                                'id_pegawai' => $data_new_pelanggan->id_pegawai,
                                                'kantor_pegawai' => $data_new_pelanggan->kantor_pegawai,
                                                'id_bagian_kantor_wilayah' => $data_kantor_wilayah->id_kantor_wilayah,
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

            try {
                $pegawai = Pegawai::where('id_pegawai', $request->update)->first();
                $pegawai->id_posisi_pegawai = $request->nama_posisi;
                $pegawai->status_pegawai = $request->status;
                $pegawai->sebagai_pegawai = $request->sebagai;
                $pegawai->update();

                return back()->with('alert', 'success_Berhasil diperbarui.');
            } catch (\Throwable $th) {
                // throw $th;

                return back()->with('alert', 'danger_Gagal menyimpan');
            }
        }
    }

    public function show($id)
    {
        $pegawai = Pegawai::with('NamaPosisi')->where('id_pegawai', $id)->where('delete_pegawai', 'N')->first();
        if (is_null($pegawai)) {
            return back();
        }
        if ($pegawai->status_data == 'local') {
            # code...
            $pegawai->telp_pegawai =  $pegawai->primary_phone;
            $pegawai->email_pegawai = $pegawai->email;
        }else{
            $pegawai->telp_pegawai =  $this->decryptssl($pegawai->primary_phone, 'P/zqOYfEDWHmQ9/g8PrApw==');
            $pegawai->email_pegawai = $this->decryptssl($pegawai->email, 'P/zqOYfEDWHmQ9/g8PrApw==');
        }


        return view('pages.user.lihat', compact('pegawai'));
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
