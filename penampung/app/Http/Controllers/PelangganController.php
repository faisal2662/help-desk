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
use Illuminate\Support\Facades\Http;
use App\Models\KantorWilayah;
use Carbon\Carbon;
use LDAP\Result;

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



        return view('pages.user.index', compact('input'));
    }
    private function decryptssl($str, $key)
    {
        $str = base64_decode($str);
        $key = base64_decode($key);
        $decrypted = openssl_decrypt($str, 'AES-128-ECB', $key,  OPENSSL_RAW_DATA);
        return $decrypted;
    }
    public function getApi(Request $request)
    {


        // $result =   $request->data;
        // return response()->json(['status' => 'success'], 200);

        // // $result =   get_api_sunfish::get_apis();
        $filePath = base_path('../data/data_api_dev_sunfish.json');
        // $data_api = null;
        $jsonContent = File::get($filePath);

        // // Mengonversi konten JSON menjadi array (true untuk mengembalikan array, false untuk objek)
        $result = json_decode(
            $jsonContent,
            true
        );

        // return response()->json($result);

        // $get_pegawai = DB::table('tb_pegawai')->pluck('id_pegawai', 'employee_id')->toArray();
        // // // $get_pegawai = DB::table('tb_pegawai')->where('id_bagian_kantor_pusat', 0)->where('id_bagian_kantor_cabang', 0)->where('id_bagian_kantor_wilayah', 0)->get();
        // // // return $get_pegawai->count();
        // $data = [];
        // $total = 0;
        // foreach ($result as $pegawai) {
        //     if (!array_key_exists($pegawai['EMPLOYEE_ID'], $get_pegawai)) {
        //         $data[] = $pegawai;
        //         $data['total'] = $total++;
        //     }
        //     # code...
        // }

        // return $data;

        $role_user = ['1', '18', '19', '45', '46', '28', '29', '31', '32', '33', '37', '38', '39', '47', '48', '50'];

        // Ambil semua posisi jabatan dari database (mapping nama ke ID)
        $bagianKantorCabang = DB::table('tb_bagian_kantor_cabang')->where('delete_bagian_kantor_cabang', 'N')->get();
        $kantorCabang = DB::table('tb_kantor_cabang')->where('delete_kantor_cabang', 'N')->pluck('id_kantor_cabang', 'nama_kantor_cabang')->toArray();
        $kantorWilayah = DB::table('tb_kantor_wilayah')->where('delete_kantor_wilayah', 'N')->pluck('id_kantor_wilayah', 'nama_kantor_wilayah')->toArray();

        // Mengambil data bagian kantor wilayah yang tidak dihapus
        $bagianKantorWilayah = DB::table('tb_bagian_kantor_wilayah')
            ->where('delete_bagian_kantor_wilayah', 'N') // Memastikan hanya mengambil yang tidak dihapus
            ->pluck('id_bagian_kantor_wilayah', 'nama_bagian_kantor_wilayah')
            ->toArray();

        $idkantorWilayah = KantorWilayah::with('BagianKantorWilayah')
            ->where('delete_kantor_wilayah', 'N')->get()->groupBy('nama_kantor_wilayah')->toArray();




        $posisiMapping = NamaPosisi::where('is_delete', 'N')->pluck('id_posisi_pegawai', 'nama_posisi')->toArray();
        // $kantorPusatNama = DB::table('tb_kantor_pusat')->where('delete_kantor_pusat', 'N')->pluck('id_kantor_pusat', 'nama_kantor_pusat')->toArray();
        $kantorPusat = DB::table('tb_kantor_pusat')->where('delete_kantor_pusat', 'N')->pluck('id_kantor_pusat', 'nama_kantor_pusat')->toArray();
        $bagianKantorPusat = DB::table('tb_bagian_kantor_pusat')->where('delete_bagian_kantor_pusat', 'N')->pluck('id_bagian_kantor_pusat', 'nama_bagian_kantor_pusat')->toArray();
        $processedBagianKantorPusat = []; // Array untuk menyimpan hasil yang diproses
        $processedBagianKantorWilayah = []; // Array untuk menyimpan hasil yang

        try {

            foreach ($bagianKantorPusat as $key => $value) {
                foreach ($kantorPusat as $kantorKey => $kantorValue) {
                    // Menggabungkan nama bagian dengan nama kantor pusat
                    $bagian_kantor_pusat = $key . ' ' . $kantorKey; // Menggabungkan dengan spasi

                    $bagian_kantor_pusat = strtolower(str_replace(' ', '', $bagian_kantor_pusat));
                    // Simpan hasil yang telah diproses, termasuk ID bagian dan ID kantor pusat
                    $processedBagianKantorPusat[$bagian_kantor_pusat] = [
                        'id_bagian' => $value, // ID bagian
                        'id_kantor' => $kantorValue, // ID kantor pusat
                    ];
                }
            }
            // Mengambil data kantor wilayah yang tidak dihapus

            // Inisialisasi array untuk menyimpan hasil yang diproses
            $processedBagianKantorWilayah = [];

            // Menggabungkan nama bagian dengan nama kantor wilayah
            foreach ($bagianKantorWilayah as $key => $value) {
                foreach ($kantorWilayah as $kantorKey => $kantorValue) {
                    // Menggabungkan nama bagian dengan nama kantor wilayah
                    $bagian_kantor_wilayah = $key . ' ' . $kantorKey; // Menggabupngkan dengan spasi

                    // Simpan hasil yang telah diproses, termasuk ID bagian dan ID kantor
                    $processedBagianKantorWilayah[$bagian_kantor_wilayah] = [
                        'id_bagian' => $value, // ID bagian
                        'id_kantor' => $kantorValue, // ID kantor wilayah
                    ];
                }
            }


            // Membuat array kunci dari posisi yang telah diproses
            $resultPosisiMapping = [];
            foreach ($posisiMapping as $key => $value) {
                $resultPosisi[preg_replace('/[^a-z0-9&.-]/i', '', strtolower($key))] = $value;

                // $resultPosisiMapping[strtolower(str_replace(' ', '', $key))] = $value; // Menggunakan nama posisi yang diproses sebagai kunci
            }

            foreach ($result as $pegawai) {
                // $text =   $pegawai['POSITION_NAME'];
                // $text = str_replace("\u00a0", " ", $text);
                // $pegawai['POSITION_NAME'] = $text;

                $posisiPegawai = preg_replace('/[^a-z0-9&.-]/i', '', strtolower($pegawai['POSITION_NAME'])); // Menghapus karakter non-alfanumerik

                $data_pegawai = DB::table('tb_pegawai')->where('delete_pegawai', 'N')->where('employee_id', $pegawai['EMPLOYEE_ID'])->first();
                if (!is_null($data_pegawai)) {
                    // Update data pegawai jika sudah ada
                    $pegawai['created_by'] = 'api-sunfish';
                    $pegawai['created_date'] = now(); // Menggunakan now() untuk mendapatkan timestamp saat ini
                    $peg = DB::table('tb_pegawai')->where('delete_pegawai', 'N')->where('employee_id', $pegawai['EMPLOYEE_ID'])->update($pegawai);
                    $get_data_pegawai = DB::table('tb_pegawai')->where('employee_id', $pegawai['EMPLOYEE_ID'])->where('id_bagian_kantor_pusat', 0)->where('id_bagian_kantor_cabang', 0)->where('id_bagian_kantor_wilayah', 0)->first();


                    if (!is_null($get_data_pegawai)) {

                        $get_kepala_unit_kerja  = DB::table('tb_kepala_unit_kerja')->where('delete_kepala_unit_kerja', 'N')->where('id_pegawai', $get_data_pegawai->id_pegawai)->first();

                        if (!is_null($get_data_pegawai) && is_null($get_kepala_unit_kerja)) {

                            $prosesDepartmentPegawai = strtolower(str_replace(' ', '', $pegawai['DEPARTMENT_NAME']));
                            if (array_key_exists($posisiPegawai, $resultPosisiMapping)) {
                                $pegawai['id_posisi_pegawai'] = $resultPosisiMapping[$posisiPegawai];
                            } else {
                                $pegawai['id_posisi_pegawai'] = null;
                            }
                            $pegawai['id_bagian_kantor_pusat'] = 0;
                            $pegawai['id_bagian_kantor_cabang'] = 0;
                            $pegawai['id_bagian_kantor_wilayah'] = 0;

                            // Menyimpan data berdasarkan kondisi
                            if ($pegawai['BRANCH_NAME'] === 'Kantor Pusat') {
                                // dd($pegawai) ;

                                if (array_key_exists($prosesDepartmentPegawai,  $processedBagianKantorPusat)) {
                                    // dd($prosesDepartmentPegawai);
                                    //  return $processedBagianKantorPusat ;
                                    // return response()->json($pegawai);

                                    $kepala = 'Kepala ' . $pegawai['DIVISION_NAME'];
                                    $pegawai['id_bagian_kantor_pusat'] = $processedBagianKantorPusat[$prosesDepartmentPegawai]['id_bagian'];
                                    $idKantorPusat = $processedBagianKantorPusat[$prosesDepartmentPegawai]['id_kantor'];
                                    $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];
                                    // return $pegawai
                                    $get_pegawai = DB::table('tb_pegawai')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update($pegawai);

                                    if ($pegawai['POSITION_NAME'] == $kepala) {

                                        $kepala_unit = DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
                                        if ($kepala_unit) {
                                            DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update([
                                                'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                                'id_bagian_kantor_pusat' => $idKantorPusat,
                                                'id_bagian_kantor_wilayah' => 0,
                                                'id_bagian_kantor_cabang' => 0,
                                            ]);
                                        } else {
                                            DB::table('tb_kepala_unit_kerja')->insert([
                                                'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                                'id_pegawai' => $get_data_pegawai->id_pegawai,
                                                'id_bagian_kantor_pusat' => $idKantorPusat,
                                                'id_bagian_kantor_cabang' => 0,
                                                'id_bagian_kantor_wilayah' => 0,
                                            ]);
                                        }
                                    }
                                } elseif (array_key_exists($pegawai['DEPARTMENT_NAME'], $kantorCabang)) {
                                    $kepala = 'Pimpinan ' . $pegawai['DIVISION_NAME'];

                                    $idKantorCabang = $kantorCabang[$pegawai['DEPARTMENT_NAME']];
                                    $bagianKantorCabang = DB::table('tb_bagian_kantor_cabang')->where('delete_bagian_kantor_cabang', 'N')->where('id_kantor_cabang', $idKantorCabang)->pluck('id_bagian_kantor_cabang', 'nama_bagian_kantor_cabang')->toArray();

                                    $sectionName = preg_replace('/KC .*/', '', $pegawai['SECTION_NAME']);
                                    $beforeSectionName = trim($sectionName);
                                    $sectionName = 'Bagian ' . $beforeSectionName;
                                    if (array_key_exists($sectionName, $bagianKantorCabang)) {

                                        $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$sectionName];
                                    } else if (strpos($beforeSectionName, 'Bagian') !== 'Bagian') {
                                        $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$beforeSectionName];
                                    } else {

                                        $pegawai['id_bagian_kantor_cabang'] = 0;
                                    }
                                    // $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$sectionName] ? $bagianKantorCabang[$sectionName] : 0;
                                    $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];

                                    $get_pegawai = DB::table('tb_pegawai')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update($pegawai);
                                    if ($pegawai['POSITION_NAME'] == $kepala) {
                                        $kepala_unit =   DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
                                        if ($kepala_unit) {
                                            DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update([
                                                'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                                'id_bagian_kantor_cabang' => $idKantorCabang,
                                                'id_bagian_kantor_pusat' => 0,
                                                'id_bagian_kantor_wilayah' => 0,
                                            ]);
                                        } else {
                                            DB::table('tb_kepala_unit_kerja')->insert([
                                                'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                                'id_bagian_kantor_cabang' => $idKantorCabang,
                                                'id_pegawai' => $get_data_pegawai->id_pegawai,
                                                'id_bagian_kantor_pusat' => 0,
                                                'id_bagian_kantor_wilayah' => 0,
                                            ]);
                                        }
                                    }
                                } elseif (array_key_exists($pegawai['DEPARTMENT_NAME'], $kantorWilayah)) {
                                    $kepala = 'Pimpinan ' . $pegawai['DIVISION_NAME'];

                                    $idKantorWilayah = $kantorWilayah[$pegawai['DEPARTMENT_NAME']];
                                    $bagianKantorWilayah = DB::table('tb_bagian_kantor_wilayah')->where('delete_bagian_kantor_wilayah', 'N')->where('id_kantor_cabang', $idKantorWilayah)->pluck('id_bagian_kantor_wilayah', 'nama_bagian_kantor_wilayah')->toArray();

                                    $sectionName = preg_replace('/KC .*/', '', $pegawai['SECTION_NAME']);
                                    $beforeSectionName = trim($sectionName);
                                    $sectionName = 'Bagian ' . $beforeSectionName;
                                    if (array_key_exists($sectionName, $bagianKantorWilayah)) {

                                        $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$sectionName];
                                    } else if (strpos($beforeSectionName, 'Bagian')) {
                                        $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$beforeSectionName];
                                    } else {

                                        $pegawai['id_bagian_kantor_wilayah'] = 0;
                                    }
                                    // $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$sectionName] ? $bagianKantorWilaah[$sectionName] : 0;
                                    $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];


                                    $get_pegawai = DB::table('tb_pegawai')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update($pegawai);

                                    if ($pegawai['POSITION_NAME'] == $kepala) {
                                        $kepala_unit =   DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
                                        if ($kepala_unit) {
                                            DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update([
                                                'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                                'id_bagian_kantor_wilayah' => $idKantorWilayah,
                                                'id_bagian_kantor_cabang' => 0,
                                                'id_bagian_kantor_pusat' => 0,
                                            ]);
                                        } else {
                                            DB::table('tb_kepala_unit_kerja')->insert([
                                                'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                                'id_pegawai' => $get_data_pegawai->id_pegawai,
                                                'id_bagian_kantor_wilayah' => $idKantorWilayah,
                                                'id_bagian_kantor_cabang' => 0,
                                                'id_bagian_kantor_pusat' => 0,
                                            ]);
                                        }
                                    }
                                } else  if (array_key_exists($pegawai['DEPARTMENT_NAME'], $kantorPusat)) {

                                    if (array_key_exists($pegawai['DEPARTMENT_NAME'], $kantorPusat)) {
                                        $kepala = 'Kepala ' . $pegawai['DIVISION_NAME'];
                                        $idKantorPusat = $kantorPusat[$pegawai['DIVISION_NAME']];
                                        // $idBagianKantorPusat = DB::table('tb_bagian_kantor_pusat')->where('id_kantor_pusat', $idKantorPusat)->where('nama_bagian_kantor_pusat', $pegawai['se']);
                                        $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];
                                        // return $pegawai;
                                        $get_pegawai = DB::table('tb_pegawai')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update($pegawai);
                                        $idKantorPusat = $kantorPusat[$pegawai['DIVISION_NAME']];
                                        // if ($pegawai['POSITION_NAME'] == $kepala) {

                                        $kepala_unit = DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
                                        if ($kepala_unit) {
                                            DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update([
                                                'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                                'id_bagian_kantor_pusat' => $idKantorPusat,
                                                'id_bagian_kantor_wilayah' => 0,
                                                'id_bagian_kantor_cabang' => 0,
                                            ]);
                                        } else {
                                            DB::table('tb_kepala_unit_kerja')->insert([
                                                'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                                'id_pegawai' => $get_data_pegawai->id_pegawai,
                                                'id_bagian_kantor_pusat' => $idKantorPusat,
                                                'id_bagian_kantor_cabang' => 0,
                                                'id_bagian_kantor_wilayah' => 0,
                                            ]);
                                        }
                                    } else {
                                        $idKantorPusat = 0;
                                    }
                                } else if (array_key_exists($pegawai['DEPARTMENT_NAME'], $bagianKantorPusat)) {
                                    if (strpos($pegawai['BRANCH_NAME'], 'Kantor Unit Pelayanan') !== false) {
                                        $posisi = str_replace('Kantor Unit Pelayanan', '', $pegawai['BRANCH_NAME']);
                                        $kepala = 'Kepala KUP' . $posisi;
                                        $sectionName = rtrim($pegawai['SECTION_NAME']);
                                        if (isset($kantorCabang[$sectionName])) {
                                            $idKantorCabang = $kantorCabang[$pegawai['SECTION_NAME']];
                                        } else {

                                            $idKantorCabang = 0;
                                        }
                                        $pegawai['id_bagian_kantor_cabang'] = $idKantorCabang;
                                        $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];
                                        $get_pegawai = DB::table('tb_pegawai')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update($pegawai);
                                        // for ($i = 0; $i < count($role_user); $i++) {
                                        //     DB::table('tb_role_user')->insert([
                                        //         'id_account' => $get_pegawai,
                                        //         'id_role_menu' => $role_user[$i],
                                        //         'can_access' => "Y",
                                        //         'can_create' => "N",
                                        //         'can_update' => "N",
                                        //         'can_delete' => "N",
                                        //         'flag' => 'GENERATE',

                                        //     ]);
                                        // }
                                        // $data[] = $pegawai;
                                        if ($pegawai['POSITION_NAME'] == $kepala) {

                                            DB::table('tb_kepala_unit_kerja')->insert([
                                                'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                                'id_pegawai' => $get_pegawai,
                                                'id_bagian_kantor_cabang' => $idKantorCabang,
                                                'id_bagian_kantor_pusat' => 0,
                                                'id_bagian_kantor_wilayah' => 0,
                                            ]);
                                        }
                                    } else {

                                        $kepala = 'Kepala ' . $pegawai['DIVISION_NAME'];
                                        $pegawai['id_bagian_kantor_pusat'] = $bagianKantorPusat[$pegawai['DEPARTMENT_NAME']];
                                        $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];
                                        $get_pegawai = DB::table('tb_pegawai')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update($pegawai);
                                        if (isset($kantorPusat[$pegawai['DIVISION_NAME']])) {

                                            $idKantorPusat = $kantorPusat[$pegawai['DIVISION_NAME']];
                                        } else {
                                            return response()->json($pegawai);
                                            $idKantorPusat = 0;
                                        }
                                        if ($pegawai['POSITION_NAME'] == $kepala) {

                                            $kepala_unit = DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
                                            if ($kepala_unit) {
                                                DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update([
                                                    'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                                    'id_bagian_kantor_pusat' => $idKantorPusat,
                                                    'id_bagian_kantor_wilayah' => 0,
                                                    'id_bagian_kantor_cabang' => 0,
                                                ]);
                                            } else {
                                                DB::table('tb_kepala_unit_kerja')->insert([
                                                    'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                                    'id_pegawai' => $get_data_pegawai->id_pegawai,
                                                    'id_bagian_kantor_pusat' => $idKantorPusat,
                                                    'id_bagian_kantor_cabang' => 0,
                                                    'id_bagian_kantor_wilayah' => 0,
                                                ]);
                                            }
                                        }
                                    }
                                }
                            } elseif (array_key_exists($pegawai['BRANCH_NAME'], $kantorCabang)) {
                                $kepala = 'Pemimpin ' . $pegawai['BRANCH_NAME'];

                                $idKantorCabang = $kantorCabang[$pegawai['BRANCH_NAME']];
                                $bagianKantorCabang = DB::table('tb_bagian_kantor_cabang')->where('delete_bagian_kantor_cabang', 'N')->where('id_kantor_cabang', $idKantorCabang)->pluck('id_bagian_kantor_cabang', 'nama_bagian_kantor_cabang')->toArray();
                                $sectionName = preg_replace('/KC .*/', '', $pegawai['SECTION_NAME']);
                                $beforeSectionName = trim($sectionName);
                                $sectionName = 'Bagian ' . $beforeSectionName;
                                if (array_key_exists($sectionName, $bagianKantorCabang)) {

                                    $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$sectionName];
                                } else if (strpos($beforeSectionName, 'Bagian') !== false) {
                                    if (isset($bagianKantorCabang[$beforeSectionName])) {

                                        $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$beforeSectionName];
                                    }
                                    if (strpos($beforeSectionName, 'KCK') !== false) {
                                        $sectionName = preg_replace('/\S*KCK.*/', '', $beforeSectionName);
                                        $beforeSectionName = trim($sectionName);
                                        // dd($pegawai);
                                        if (isset($bagianKantorCabang[$beforeSectionName])) {

                                            $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$beforeSectionName];
                                        }
                                    }
                                } else {

                                    $pegawai['id_bagian_kantor_cabang'] = 0;
                                }
                                $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];

                                $get_pegawai = DB::table('tb_pegawai')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update($pegawai);

                                // $data[] = $pegawai;
                                if ($pegawai['POSITION_NAME'] == $kepala) {

                                    $kepala_unit = DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
                                    if ($kepala_unit) {
                                        DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update([
                                            'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                            'id_bagian_kantor_cabang' => $idKantorCabang,
                                            'id_bagian_kantor_pusat' => 0,
                                            'id_bagian_kantor_wilayah' => 0,
                                        ]);
                                    } else {
                                        DB::table('tb_kepala_unit_kerja')->insert([
                                            'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                            'id_bagian_kantor_cabang' => $idKantorCabang,
                                            'id_pegawai' => $get_data_pegawai->id_pegawai,
                                            'id_bagian_kantor_pusat' => 0,
                                            'id_bagian_kantor_wilayah' => 0,
                                        ]);
                                    }
                                }
                            } elseif (array_key_exists($pegawai['BRANCH_NAME'], $kantorWilayah)) {
                                $kepala = 'Pimpinan ' . $pegawai['DIVISION_NAME'];

                                $idKantorWilayah = $kantorWilayah[$pegawai['BRANCH_NAME']];
                                $bagianKantorWilayah = DB::table('tb_bagian_kantor_wilayah')->where('delete_bagian_kantor_wilayah', 'N')->where('id_kantor_wilayah', $idKantorWilayah)->pluck('id_bagian_kantor_wilayah', 'nama_bagian_kantor_wilayah')->toArray();
                                $sectionName = preg_replace('/KW .*/', '', $pegawai['SECTION_NAME']);
                                $beforeSectionName = trim($sectionName);

                                $sectionName = 'Bagian ' . $beforeSectionName;
                                if (array_key_exists($sectionName, $bagianKantorWilayah)) {

                                    $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$sectionName];
                                } else if (strpos($beforeSectionName, 'Bagian') !== false) {
                                    if (isset($bagianKantorWilayah[$beforeSectionName])) {

                                        $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$beforeSectionName];
                                    }
                                    if (isset($processedBagianKantorWilayah[$beforeSectionName]['id_bagian'])) {

                                        $pegawai['id_bagian_kantor_wilayah'] = $processedBagianKantorWilayah[$beforeSectionName]['id_bagian'];
                                    }
                                } else {

                                    $pegawai['id_bagian_kantor_wilayah'] = 0;
                                }
                                // $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$sectionName] ? $bagianKantorWilayah[$sectionName] :0;
                                $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];

                                $get_pegawai = DB::table('tb_pegawai')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update($pegawai);
                                if ($pegawai['POSITION_NAME'] == $kepala) {
                                    $kepala_unit =   DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
                                    if ($kepala_unit) {
                                        DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update([
                                            'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                            'id_bagian_kantor_wilayah' => $idKantorWilayah,
                                            'id_bagian_kantor_cabang' => 0,
                                            'id_bagian_kantor_pusat' => 0,
                                        ]);
                                    } else {
                                        DB::table('tb_kepala_unit_kerja')->insert([
                                            'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                            'id_pegawai' => $get_data_pegawai->id_pegawai,
                                            'id_bagian_kantor_wilayah' => $idKantorWilayah,
                                            'id_bagian_kantor_cabang' => 0,
                                            'id_bagian_kantor_pusat' => 0,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
                else {
                    // Menyimpan data pegawai baru
                    $pegawai['id_bagian_kantor_pusat'] = 0;
                    $pegawai['id_bagian_kantor_cabang'] = 0;
                    $pegawai['id_bagian_kantor_wilayah'] = 0;
                    $pegawai['sebagai_pegawai'] = "Staff";
                    $pegawai['status_pegawai'] = "Aktif";
                    $pegawai['created_date'] = now();
                    $pegawai['created_by'] = 'api-sunfish';

                    // Menentukan ID posisi pegawai
                    $prosesDepartmentPegawai = strtolower(str_replace(' ', '', $pegawai['DEPARTMENT_NAME']));
                    if (array_key_exists($posisiPegawai, $resultPosisiMapping)) {
                        $pegawai['id_posisi_pegawai'] = $resultPosisiMapping[$posisiPegawai];
                    } else {
                        $pegawai['id_posisi_pegawai'] = null;
                    }


                    // Menyimpan data berdasarkan kondisi
                    if ($pegawai['BRANCH_NAME'] == 'Kantor Pusat') {
                        // dd($pegawai) ;
                        if (array_key_exists($prosesDepartmentPegawai,  $processedBagianKantorPusat)) {
                            //  return $processedBagianKantorPusat ;

                            $kepala = 'Kepala ' . $pegawai['DIVISION_NAME'];
                            $pegawai['id_bagian_kantor_pusat'] = $processedBagianKantorPusat[$prosesDepartmentPegawai]['id_bagian'];
                            $idKantorPusat = $processedBagianKantorPusat[$prosesDepartmentPegawai]['id_kantor'];
                            $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];
                            // return $pegawai;
                            $get_pegawai = DB::table('tb_pegawai')->insertGetId($pegawai);
                            for ($i = 0; $i < count($role_user); $i++) {
                                DB::table('tb_role_user')->insert([
                                    'id_account' => $get_pegawai,
                                    'id_role_menu' => $role_user[$i],
                                    'can_access' => "Y",
                                    'can_create' => "N",
                                    'can_update' => "N",
                                    'can_delete' => "N",

                                ]);
                            }
                            if ($pegawai['POSITION_NAME'] == $kepala) {
                                DB::table('tb_kepala_unit_kerja')->insert([
                                    'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                    'id_pegawai' => $get_pegawai,
                                    'id_bagian_kantor_pusat' => $idKantorPusat,
                                    'id_bagian_kantor_pusat' => 0,
                                    'id_bagian_kantor_wilayah' => 0,
                                ]);
                            }
                        } elseif (array_key_exists($pegawai['DEPARTMENT_NAME'], $kantorCabang)) {
                            $kepala = 'Pimpinan ' . $pegawai['DIVISION_NAME'];

                            $idKantorCabang = $kantorCabang[$pegawai['DEPARTMENT_NAME']];
                            $bagianKantorCabang = DB::table('tb_bagian_kantor_cabang')->where('delete_bagian_kantor_cabang', 'N')->where('id_kantor_cabang', $idKantorCabang)->pluck('id_bagian_kantor_cabang', 'nama_bagian_kantor_cabang')->toArray();

                            $sectionName = preg_replace('/KC .*/', '', $pegawai['SECTION_NAME']);
                            $beforeSectionName = trim($sectionName);
                            $sectionName = 'Bagian ' . $beforeSectionName;
                            if (array_key_exists($sectionName, $bagianKantorCabang)) {

                                $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$sectionName];
                            } else if (strpos($beforeSectionName, 'Bagian') !== 'Bagian') {
                                $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$beforeSectionName];
                            } else {

                                $pegawai['id_bagian_kantor_cabang'] = 0;
                            }
                            // $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$sectionName] ? $bagianKantorCabang[$sectionName] : 0;
                            $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];

                            $get_pegawai = DB::table('tb_pegawai')->insertGetId($pegawai);
                            for ($i = 0; $i < count($role_user); $i++) {
                                DB::table('tb_role_user')->insert([
                                    'id_account' => $get_pegawai,
                                    'id_role_menu' => $role_user[$i],
                                    'can_access' => "Y",
                                    'can_create' => "N",
                                    'can_update' => "N",
                                    'can_delete' => "N",
                                    'flag' => 'GENERATE',

                                ]);
                            }
                            if ($pegawai['POSITION_NAME'] == $kepala) {
                                DB::table('tb_kepala_unit_kerja')->insert([
                                    'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                    'id_pegawai' => $get_pegawai,
                                    'id_bagian_kantor_cabang' => $idKantorCabang,
                                    'id_bagian_kantor_pusat' => 0,
                                    'id_bagian_kantor_wilayah' => 0,
                                ]);
                            }
                        } elseif (array_key_exists($pegawai['DEPARTMENT_NAME'], $kantorWilayah)) {
                            $kepala = 'Pimpinan ' . $pegawai['DIVISION_NAME'];

                            $idKantorWilayah = $kantorWilayah[$pegawai['DEPARTMENT_NAME']];
                            $bagianKantorWilayah = DB::table('tb_bagian_kantor_wilayah')->where('delete_bagian_kantor_wilayah', 'N')->where('id_kantor_cabang', $idKantorWilayah)->pluck('id_bagian_kantor_wilayah', 'nama_bagian_kantor_wilayah')->toArray();

                            $sectionName = preg_replace('/KC .*/', '', $pegawai['SECTION_NAME']);
                            $beforeSectionName = trim($sectionName);
                            $sectionName = 'Bagian ' . $beforeSectionName;
                            if (array_key_exists($sectionName, $bagianKantorWilayah)) {

                                $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$sectionName];
                            } else if (strpos($beforeSectionName, 'Bagian')) {
                                $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$beforeSectionName];
                            } else {

                                $pegawai['id_bagian_kantor_wilayah'] = 0;
                            }
                            // $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$sectionName] ? $bagianKantorWilaah[$sectionName] : 0;
                            $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];


                            $get_pegawai = DB::table('tb_pegawai')->insertGetId($pegawai);
                            for ($i = 0; $i < count($role_user); $i++) {
                                DB::table('tb_role_user')->insert([
                                    'id_account' => $get_pegawai,
                                    'id_role_menu' => $role_user[$i],
                                    'can_access' => "Y",
                                    'can_create' => "N",
                                    'can_update' => "N",
                                    'can_delete' => "N",
                                    'flag' => 'GENERATE',


                                ]);
                            }
                            if ($pegawai['POSITION_NAME'] == $kepala) {
                                DB::table('tb_kepala_unit_kerja')->insert([
                                    'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                    'id_pegawai' => $get_pegawai,
                                    'id_bagian_kantor_wilayah' => $idKantorWilayah,
                                    'id_bagian_kantor_pusat' => 0,
                                    'id_bagian_kantor_cabang' => 0,
                                ]);
                            }
                        } else  if (array_key_exists($pegawai['DEPARTMENT_NAME'], $kantorPusat)) {

                            if (array_key_exists($pegawai['DEPARTMENT_NAME'], $kantorPusat)) {
                                $kepala = 'Kepala ' . $pegawai['DIVISION_NAME'];



                                $idKantorPusat = $kantorPusat[$pegawai['DIVISION_NAME']];
                                // $idBagianKantorPusat = DB::table('tb_bagian_kantor_pusat')->where('id_kantor_pusat', $idKantorPusat)->where('nama_bagian_kantor_pusat', $pegawai['se']);
                                $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];
                                // return $pegawai;
                                $get_pegawai = DB::table('tb_pegawai')->insertGetId($pegawai);
                                for ($i = 0; $i < count($role_user); $i++) {
                                    DB::table('tb_role_user')->insert([
                                        'id_account' => $get_pegawai,
                                        'id_role_menu' => $role_user[$i],
                                        'can_access' => "Y",
                                        'can_create' => "N",
                                        'can_update' => "N",
                                        'can_delete' => "N",
                                        'flag' => 'GENERATE',

                                    ]);
                                }
                                $idKantorPusat = $kantorPusat[$pegawai['DIVISION_NAME']];
                                if ($pegawai['POSITION_NAME'] == $kepala) {
                                    DB::table('tb_kepala_unit_kerja')->insert([
                                        'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                        'id_pegawai' => $get_pegawai,
                                        'id_bagian_kantor_pusat' => $idKantorPusat,
                                        'id_bagian_kantor_cabang' => 0,
                                        'id_bagian_kantor_wilayah' => 0,
                                    ]);
                                }
                                // return $pegawai;



                            } else {
                                $idKantorPusat = 0;
                            }
                        } else if (array_key_exists($pegawai['DEPARTMENT_NAME'], $bagianKantorPusat)) {

                            $kepala = 'Kepala ' . $pegawai['DIVISION_NAME'];
                            $pegawai['id_bagian_kantor_pusat'] = $bagianKantorPusat[$pegawai['DEPARTMENT_NAME']];
                            $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];
                            $get_pegawai = DB::table('tb_pegawai')->insertGetId($pegawai);
                            for ($i = 0; $i < count($role_user); $i++) {
                                DB::table('tb_role_user')->insert([
                                    'id_account' => $get_pegawai,
                                    'id_role_menu' => $role_user[$i],
                                    'can_access' => "Y",
                                    'can_create' => "N",
                                    'can_update' => "N",
                                    'can_delete' => "N",
                                    'flag' => 'GENERATE',

                                ]);
                            }
                            // dd($pegawai);
                            if (isset($kantoPusat[$pegawai['DIVISION_NAME']])) {
                                $idKantorPusat = $kantorPusat[$pegawai['DIVISION_NAME']];
                            } else {

                                $idKantorPusat = 0;
                            }
                            if ($pegawai['POSITION_NAME'] == $kepala) {
                                DB::table('tb_kepala_unit_kerja')->insert([
                                    'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                    'id_pegawai' => $get_pegawai,
                                    'id_bagian_kantor_pusat' => $idKantorPusat,
                                    'id_bagian_kantor_cabang' => 0,
                                    'id_bagian_kantor_wilayah' => 0,
                                ]);
                            }
                        }
                    } elseif (array_key_exists($pegawai['BRANCH_NAME'], $kantorCabang)) {
                        if (strpos($pegawai['BRANCH_NAME'], 'Kantor Unit Pelayanan') !== false) {
                            $posisi = str_replace('Kantor Unit Pelayanan', '', $pegawai['BRANCH_NAME']);
                            $kepala = 'Kepala KUP' . $posisi;
                            $sectionName = rtrim($pegawai['SECTION_NAME']);
                            if (isset($kantorCabang[$sectionName])) {
                                $idKantorCabang = $kantorCabang[$pegawai['SECTION_NAME']];
                            } else {

                                $idKantorCabang = 0;
                            }
                            $pegawai['id_bagian_kantor_cabang'] = $idKantorCabang;
                            $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];

                            $get_pegawai = DB::table('tb_pegawai')->insertGetId($pegawai);
                            for ($i = 0; $i < count($role_user); $i++) {
                                DB::table('tb_role_user')->insert([
                                    'id_account' => $get_pegawai,
                                    'id_role_menu' => $role_user[$i],
                                    'can_access' => "Y",
                                    'can_create' => "N",
                                    'can_update' => "N",
                                    'can_delete' => "N",
                                    'flag' => 'GENERATE',

                                ]);
                            }
                            // $data[] = $pegawai;
                            if ($pegawai['POSITION_NAME'] == $kepala) {

                                DB::table('tb_kepala_unit_kerja')->insert([
                                    'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                    'id_pegawai' => $get_pegawai,
                                    'id_bagian_kantor_cabang' => $idKantorCabang,
                                    'id_bagian_kantor_pusat' => 0,
                                    'id_bagian_kantor_wilayah' => 0,
                                ]);
                            }
                        } else {
                            $kepala = 'Pemimpin ' . $pegawai['BRANCH_NAME'];

                            $idKantorCabang = $kantorCabang[$pegawai['BRANCH_NAME']];
                            $bagianKantorCabang = DB::table('tb_bagian_kantor_cabang')->where('delete_bagian_kantor_cabang', 'N')->where('id_kantor_cabang', $idKantorCabang)->pluck('id_bagian_kantor_cabang', 'nama_bagian_kantor_cabang')->toArray();
                            $sectionName = preg_replace('/KC .*/', '', $pegawai['SECTION_NAME']);
                            $beforeSectionName = trim($sectionName);
                            $sectionName = 'Bagian ' . $beforeSectionName;
                            if (array_key_exists($sectionName, $bagianKantorCabang)) {

                                $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$sectionName];
                            } else if (strpos($beforeSectionName, 'Bagian') !== false) {
                                if (isset($bagianKantorCabang[$beforeSectionName])) {

                                    $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$beforeSectionName];
                                }
                                if (strpos($beforeSectionName, 'KCK') !== false) {
                                    $sectionName = preg_replace('/\S*KCK.*/', '', $beforeSectionName);
                                    $beforeSectionName = trim($sectionName);
                                    // dd($pegawai);
                                    if (isset($bagianKantorCabang[$beforeSectionName])) {

                                        $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$beforeSectionName];
                                    }
                                }
                            } else {

                                $pegawai['id_bagian_kantor_cabang'] = 0;
                            }
                            $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];

                            $get_pegawai = DB::table('tb_pegawai')->insertGetId($pegawai);
                            for ($i = 0; $i < count($role_user); $i++) {
                                DB::table('tb_role_user')->insert([
                                    'id_account' => $get_pegawai,
                                    'id_role_menu' => $role_user[$i],
                                    'can_access' => "Y",
                                    'can_create' => "N",
                                    'can_update' => "N",
                                    'can_delete' => "N",
                                    'flag' => 'GENERATE',

                                ]);
                            }
                            $data[] = $pegawai;
                            if ($pegawai['POSITION_NAME'] == $kepala) {
                                DB::table('tb_kepala_unit_kerja')->insert([
                                    'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                    'id_pegawai' => $get_pegawai,
                                    'id_bagian_kantor_cabang' => $idKantorCabang,
                                    'id_bagian_kantor_pusat' => 0,
                                    'id_bagian_kantor_wilayah' => 0,
                                ]);
                            }
                        }
                    } elseif (array_key_exists($pegawai['BRANCH_NAME'], $kantorWilayah)) {
                        $kepala = 'Pimpinan ' . $pegawai['DIVISION_NAME'];

                        $idKantorWilayah = $kantorWilayah[$pegawai['BRANCH_NAME']];
                        $bagianKantorWilayah = DB::table('tb_bagian_kantor_wilayah')->where('delete_bagian_kantor_wilayah', 'N')->where('id_kantor_wilayah', $idKantorWilayah)->pluck('id_bagian_kantor_wilayah', 'nama_bagian_kantor_wilayah')->toArray();
                        $sectionName = preg_replace('/KW .*/', '', $pegawai['SECTION_NAME']);
                        $beforeSectionName = trim($sectionName);

                        $sectionName = 'Bagian ' . $beforeSectionName;
                        if (array_key_exists($sectionName, $bagianKantorWilayah)) {

                            $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$sectionName];
                        } else if (strpos($beforeSectionName, 'Bagian') !== false) {
                            if (isset($bagianKantorWilayah[$beforeSectionName])) {

                                $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$beforeSectionName];
                            }
                            if (isset($processedBagianKantorWilayah[$beforeSectionName]['id_bagian'])) {

                                $pegawai['id_bagian_kantor_wilayah'] = $processedBagianKantorWilayah[$beforeSectionName]['id_bagian'];
                            }
                        } else {

                            $pegawai['id_bagian_kantor_wilayah'] = 0;
                        }
                        // $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$sectionName] ? $bagianKantorWilayah[$sectionName] :0;
                        $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];

                        $get_pegawai = DB::table('tb_pegawai')->insertGetId($pegawai);
                        for ($i = 0; $i < count($role_user); $i++) {
                            DB::table('tb_role_user')->insert([
                                'id_account' => $get_pegawai,
                                'id_role_menu' => $role_user[$i],
                                'can_access' => "Y",
                                'can_create' => "N",
                                'can_update' => "N",
                                'can_delete' => "N",
                                'flag' => 'GENERATE',

                            ]);
                        }
                        if ($pegawai['POSITION_NAME'] == $kepala) {
                            DB::table('tb_kepala_unit_kerja')->insert([
                                'kantor_pegawai' => $pegawai['BRANCH_NAME'],
                                'id_pegawai' => $get_pegawai,
                                'id_bagian_kantor_wilayah' => $idKantorWilayah,
                                'id_bagian_kantor_cabang' => 0,
                                'id_bagian_kantor_pusat' => 0,
                            ]);
                        }
                    }
                }
            }
            return response()->json(['status' => 'ssuccess'], 200);
        } catch (\Throwable $th) {
            throw $th;
            return response()->json(['status' => $th->getMessages]);
            die;
            // return response()->json(['status' => 'gagal']);
        }

        // Simpan semua data ke database dalam satu query (opsional)
        // Pegawai::insert($data);

        // Debug hasil data
        // dd($data);
        // }



        // // Menampilkan data (misalnya)
        // // return response()->json($data);
        // else {
        //     dd('tidak ditemukan');
        //     return response()->json(['error' => 'File tidak ditemukan'], 404);
        // }
    }

    public function posisi()
    {


        $pegawai = Pegawai::where('delete_pegawai', 'N')->get();

        $posisi  = DB::table('tb_posisi_pegawai')->where('is_delete', 'N')->pluck('id_posisi_pegawai', 'nama_posisi')->toArray();

        $resultPosisi = [];
        $unPosisi = [];

        foreach ($posisi as $key => $value) {
            $resultPosisi[strtolower(str_replace(' ', '', $key))] = $value; // Menggunakan nama posisi yang diproses sebagai kunci
        }


        foreach ($pegawai as $data) {
            $posisiPegawai = strtolower(str_replace(' ', '', $data->position_name));
            if ($posisiPegawai) {
                if (array_key_exists($posisiPegawai, $resultPosisi)) {

                    $id_posisi = $resultPosisi[$posisiPegawai];
                    Pegawai::where('id_pegawai', $data->id_pegawai)->first()->update(['id_posisi_pegawai' => $id_posisi]);
                } else {
                    $unPosisi[] = $data->position_name;
                }
            }
        }
        return $unPosisi;
    }
    public function lastSync()
    {
        $date = DB::table('tb_pegawai')->orderBy('updated_date', 'desc')->first('updated_date');
        $date = Carbon::parse($date->updated_date)->translatedFormat('l, d F Y h:i');
        return response()->json(['data' => $date], 200);
    }
    public function datatables(Request $request)
    {
        $role = $this->role->role(Auth::user()->id_pegawai, "", $this->route);
        $pelanggan = Pegawai::with('NamaPosisi')
            ->where([['tb_pegawai.delete_pegawai', 'N']])
            ->orderBy('tb_pegawai.id_pegawai', 'DESC')
            ->get();

        $kantorCabang = DB::table('tb_kantor_cabang')->where('delete_kantor_cabang', 'N')->pluck('id_kantor_cabang', 'nama_kantor_cabang')->toArray();
        $kantorWilayah  = DB::table('tb_kantor_wilayah')->where('delete_kantor_wilayah', 'N')->pluck('id_kantor_wilayah', 'nama_kantor_wilayah')->toArray();

        // $filePath = base_path('../data/data_full.json');

        // $jsonContent = File::get($filePath);

        //         // Mengonversi konten JSON menjadi array
        // $pelanggan = json_decode($jsonContent, true);
        $no = 1;
        foreach ($pelanggan as $data) {

            $data->kantor = '-';
            $data->bagian = '-';

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
                                $data->kantor = $data_kantor_bagian->nama_kantor_pusat;
                                $data->bagian = 'Semua Bagian';
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
                            $data->kantor = $data_kantor_bagian->nama_kantor_pusat;
                            $data->bagian = $data_kantor_bagian->nama_bagian_kantor_pusat;
                        }
                    }
                }
            } else if (array_key_exists($data->kantor_pegawai, $kantorCabang)) {

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
                                $data->kantor = $data_kantor_bagian->nama_kantor_cabang;
                                $data->bagian = 'Semua Bagian';
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
                            $data->kantor = $data_kantor_bagian->nama_kantor_cabang;
                            $data->bagian = $data_kantor_bagian->nama_bagian_kantor_cabang;
                        }
                    }
                }
            } else if (array_key_exists($data->kantor_pegawai, $kantorWilayah)) {

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
                                $data->kantor = $data_kantor_bagian->nama_kantor_wilayah;
                                $data->bagian = 'Semua Bagian';
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
                            $data->kantor = $data_kantor_bagian->nama_kantor_wilayah;
                            $data->bagian = $data_kantor_bagian->nama_bagian_kantor_wilayah;
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
                        <img src="' . asset('logos/avatar.png') . '" style="width: 20px;height: 20px;border-radius: 100%;">
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



            // $data->kantor_pegawai = $data->branch_name;
            // $data->bagian = $data->department_name;
            $data->level_pegawai = $data->NamaPosisi ? $data->NamaPosisi->sebagai_posisi : ' ';
            $data->status_pegawai = $data->status_pegawai;
            $data->tgl_pegawai = date('j F Y, H:i', strtotime($data->created_date));
            $data->action = $update_data . "&nbsp;" . $delete_data;
        }
        // return $pelanggan;

        return DataTables::of($pelanggan)->escapecolumns([])->make(true);
    }
    public function jabatan()
    {

        $pegawai = DB::table('tb_pegawai')->where('id_posisi_pegawai')->where('delete_pegawai', 'N')->get();
        $no = 1;
        foreach ($pegawai as $data) {
            # code...
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

        if ($pegawai->status_data == 'local') {
            $pegawai->primary_phone = $pegawai->primary_phone;
            $pegawai->email = $pegawai->email;
        } else {
            $pegawai->primary_phone =  $this->decryptssl($pegawai->primary_phone, 'P/zqOYfEDWHmQ9/g8PrApw==');
            $pegawai->email = $this->decryptssl($pegawai->email, 'P/zqOYfEDWHmQ9/g8PrApw==');
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
            $kantor = '';
            if ($kepala_unit_kerja->id_bagian_kantor_pusat != 0) {
                $bagian =  $kantor_pusat->where('id_kantor_pusat', $kepala_unit_kerja->id_bagian_kantor_pusat)->first();
                $id_unit_kerja = 'Pusat,' . $bagian->id_kantor_pusat;
                $bagian_unit = 'Semua Bagian';
                $id_bagian = '0';
                $kantor  = 'Pusat';
            } elseif ($kepala_unit_kerja->id_bagian_kantor_cabang != 0) {
                $bagian = $kantor_cabang->where('id_kantor_cabang', $kepala_unit_kerja->id_bagian_kantor_cabang)->first();
                $id_unit_kerja = 'Cabang,' .  $bagian->id_kantor_cabang;
                $bagian_unit = 'Semua Bagian';
                $id_bagian = '0';
                $kantor = 'Cabang';
            }
            if ($kepala_unit_kerja->id_bagian_kantor_wilayah != 0) {
                $bagian = $kantor_wilayah->where('id_kantor_wilayah', $kepala_unit_kerja->id_bagian_kantor_wilayah)->first();
                $id_unit_kerja = 'Wilayah,' .  $bagian->id_kantor_wilayah;
                $bagian_unit = 'Semua Bagian';
                $id_bagian = '0';
                $kantor = 'Wilayah';
            }

            return view('pages.user.ubah', compact('kantor', 'unit_kerja', 'nama_posisi', 'pegawai', 'id_unit_kerja', 'bagian_unit', 'id_bagian', 'kantor_pusat', 'kantor_cabang', 'kantor_wilayah', 'bagian_kantor_pusat', 'bagian_kantor_cabang', 'bagian_kantor_wilayah'));
        }

        $id_unit_kerja = '';
        $bagian_unit = '';
        $id_bagian = '';
        $kantor = '';
        if ($pegawai->id_bagian_kantor_pusat != 0) {
            $bagian =  $bagian_kantor_pusat->where('id_bagian_kantor_pusat', $pegawai->id_bagian_kantor_pusat)->first();
            if (!is_null($bagian)) {
                $kantor = 'Pusat';
                $id_unit_kerja = 'Pusat,' . $bagian->id_kantor_pusat;
                $bagian_unit = $bagian->nama_bagian_kantor_pusat;
                $id_bagian = $bagian->nama_bagian_kantor_pusat;
            }
        } elseif ($pegawai->id_bagian_kantor_cabang != 0) {
            $bagian = $bagian_kantor_cabang->where('id_bagian_kantor_cabang', $pegawai->id_bagian_kantor_cabang)->first();
            if (!is_null($bagian)) {
                $kantor = 'Cabang';
                $id_unit_kerja = 'Cabang,' .  $bagian->id_kantor_cabang;
                $bagian_unit = $bagian->nama_bagian_kantor_cabang;
                $id_bagian = $bagian->id_bagian_kantor_cabang;
            }
        } elseif ($pegawai->id_bagian_kantor_wilayah != 0) {
            $bagian = $bagian_kantor_wilayah->where('id_bagian_kantor_wilayah', $pegawai->id_bagian_kantor_wilayah)->first();
            if (!is_null($bagian)) {
                $kantor  = 'Wilayah';
                $id_unit_kerja = 'Wilayah,' .  $bagian->id_kantor_wilayah;
                $bagian_unit = $bagian->nama_bagian_kantor_wilayah;
                $id_bagian = $bagian->id_bagian_kantor_wilayah;
            }
        }



        return view('pages.user.ubah', compact('kantor', 'unit_kerja', 'pegawai', 'nama_posisi', 'id_unit_kerja', 'bagian_unit', 'id_bagian', 'kantor_pusat', 'kantor_cabang', 'kantor_wilayah', 'bagian_kantor_pusat', 'bagian_kantor_cabang', 'bagian_kantor_wilayah'));
    }

    public function get_posisi(Request $request)
    {
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
            $data->nama_pegawai = $data->employee_name;
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

        $kantor = explode(',', $request->kantor);
        $bagian = $request->bagian;
        $id_kantor = $kantor[1];
        $kantor =  $kantor[0];
        $bagian_kantor_pusat = 0;
        $bagian_kantor_cabang = 0;
        $bagian_kantor_wilayah = 0;
        $id_kantor_pusat = 0;
        $id_kantor_cabang = 0;
        $id_kantor_wilayah = 0;
        if ($kantor == 'Pusat') {
            $kantor = 'Kantor ' . $kantor;
            $kantor_pusat = DB::table('tb_kantor_pusat')->where('delete_kantor_pusat', 'N')->where('id_kantor_pusat', $id_kantor)->first();
            $id_kantor_pusat = $kantor_pusat->id_kantor_pusat;
            $bagian_kantor_pusat = $bagian;
        } else if ($kantor == 'Cabang') {
            $kantor_cabang  = DB::table('tb_kantor_cabang')->where('delete_kantor_cabang', 'N')->where('id_kantor_cabang', $id_kantor)->first();
            $id_kantor_cabang = $kantor_cabang->id_kantor_cabang;
            $kantor = $kantor_cabang->nama_kantor_cabang;
            $bagian_kantor_cabang = $bagian;
        } elseif ($kantor  == 'Wilayah') {
            $kantor_wilayah = DB::table('tb_kantor_wilayah')->where('delete_kantor_wilayah', 'N')->where('id_kantor_wilayah', $id_kantor)->first();
            $id_kantor_wilayah = $kantor_wilayah->id_kantor_wilayah;
            $kantor = $kantor_wilayah->nama_kantor_wilayah;
            $bagian_kantor_wilayah = $bagian;
        }

        try {
            // Mengambil pegawai berdasarkan ID
            $pegawai = Pegawai::where('id_pegawai', $request->id_pegawai)->first();

            // Memperbarui informasi pegawai
            $pegawai->id_posisi_pegawai = $request->nama_posisi;
            $pegawai->kantor_pegawai = $kantor;
            $pegawai->id_bagian_kantor_pusat = $bagian_kantor_pusat;
            $pegawai->id_bagian_kantor_cabang = $bagian_kantor_cabang;
            $pegawai->id_bagian_kantor_wilayah = $bagian_kantor_wilayah;
            $pegawai->status_pegawai = $request->status;
            $pegawai->sebagai_pegawai = $request->sebagai;
            $pegawai->update(); // Simpan perubahan

            // Mengambil kepala unit kerja
            $get_kepala_unit = DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $pegawai->id_pegawai)->first();

            // Jika kepala unit kerja ada
            if ($get_kepala_unit) {
                if ($bagian != '0') {
                    // Menghapus kepala unit kerja jika bagian tidak sama dengan '0'
                    DB::table('tb_kepala_unit_kerja')->where('id_kepala_unit_kerja', $get_kepala_unit->id_kepala_unit_kerja)->update([
                        'delete_kepala_unit_kerja' => 'Y'
                    ]);
                }
            }

            // Jika bagian sama dengan '0'
            if ($bagian == '0') {
                if ($get_kepala_unit) {
                    // Memperbarui kepala unit kerja yang sudah ada
                    DB::table('tb_kepala_unit_kerja')->where('id_kepala_unit_kerja', $get_kepala_unit->id_kepala_unit_kerja)->update([
                        'kantor_pegawai' => $kantor,
                        'id_pegawai' => $pegawai->id_pegawai,
                        'id_bagian_kantor_cabang' => $id_kantor_cabang,
                        'id_bagian_kantor_pusat' => $id_kantor_pusat,
                        'id_bagian_kantor_wilayah' => $id_kantor_wilayah,
                        'delete_kepala_unit_kerja' => 'N'
                    ]);
                } else {
                    // Menyimpan kepala unit kerja baru jika tidak ada
                    DB::table('tb_kepala_unit_kerja')->insert([
                        'kantor_pegawai' => $kantor,
                        'id_pegawai' => $pegawai->id_pegawai,
                        'id_bagian_kantor_cabang' => $id_kantor_cabang,
                        'id_bagian_kantor_pusat' => $id_kantor_pusat,
                        'id_bagian_kantor_wilayah' => $id_kantor_wilayah,
                    ]);
                }
            }
            // dd($pegawai);
            return back()->with('alert', 'success_Berhasil diperbarui.');
        } catch (\Throwable $th) {
            // Tangani kesalahan
            // dd($th->getMessage());
            Log::error('Error updating pegawai or kepala unit kerja: ' . $th->getMessage());
            return back()->with('alert', 'danger_Gagal menyimpan');
        }



        // if ($pegawai->count() < 1) {
        //     return back();
        // } else {

        // try {
        //     $pegawai = Pegawai::where('id_pegawai', $request->id_pegawai)->first();
        //     $pegawai->id_posisi_pegawai = $request->nama_posisi;
        //     $pegawai->kantor_pegawai = $kantor;
        //     $pegawai->id_bagian_kantor_pusat = $bagian_kantor_pusat;
        //     $pegawai->id_bagian_kantor_cabang = $bagian_kantor_cabang;
        //     $pegawai->id_bagian_kantor_wilayah = $bagian_kantor_wilayah;
        //     $pegawai->status_pegawai = $request->status;
        //     $pegawai->sebagai_pegawai = $request->sebagai;
        //     $pegawai->update();
        //     $get_kepala_unit  = DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $pegawai->id_pegawai)->first();
        //     if ($get_kepala_unit) {
        //         if ($bagian != '0') {
        //             DB::table('tb_kepala_unit_kerja')->where('id_kepala_unit_kerja', $get_kepala_unit)->update([
        //                 'delete_kepala_unit_kerja' => 'Y'
        //             ]);
        //         }
        //     }
        //     if ($bagian == '0') {
        //         if ($get_kepala_unit) {
        //             try {
        //                 // Menyimpan data menggunakan Query Builder
        //                 DB::table('tb_kepala_unit_kerja')->where('id_kepala_unit_kerja', $get_kepala_unit->id_kepala_unit_kerja)->update([
        //                     'kantor_pegawai' => $kantor, // Ganti dengan nilai yang sesuai
        //                     'id_pegawai' => $pegawai->id_pegawai, // Ganti dengan ID pegawai yang sesuai
        //                     'id_bagian_kantor_cabang' => $bagian_kantor_cabang, // Ganti dengan ID yang sesuai
        //                     'id_bagian_kantor_pusat' => $bagian_kantor_pusat, // Atau nilai yang sesuai
        //                     'id_bagian_kantor_wilayah' => $bagian_kantor_wilayah, // Atau nilai yang sesuai

        //                 ]);
        //             } catch (\Throwable $th) {
        //                 // Tangani kesalahan
        //                 dd($th->getMessage());
        //                 // Log::error('Error saving kepala unit kerja: ' . $th->getMessage());
        //                 // return response()->json(['error' => 'An error occurred while saving data.'], 500);
        //             }
        //         } else {

        //             try {
        //                 // Menyimpan data menggunakan Query Builder
        //                 DB::table('tb_kepala_unit_kerja')->insert([
        //                     'kantor_pegawai' => $kantor, // Ganti dengan nilai yang sesuai
        //                     'id_pegawai' => $pegawai->id_pegawai, // Ganti dengan ID pegawai yang sesuai
        //                     'id_bagian_kantor_cabang' => $bagian_kantor_cabang, // Ganti dengan ID yang sesuai
        //                     'id_bagian_kantor_pusat' => $bagian_kantor_pusat, // Atau nilai yang sesuai
        //                     'id_bagian_kantor_wilayah' => $bagian_kantor_wilayah, // Atau nilai yang sesuai

        //                 ]);
        //             } catch (\Throwable $th) {
        //                 dd($th->getMessage());

        //                 // Tangani kesalahan
        //                 // Log::error('Error saving kepala unit kerja: ' . $th->getMessage());
        //                 // return response()->json(['error' => 'An error occurred while saving data.'], 500);
        //             }
        //         }
        //     }
        //     return back()->with('alert', 'success_Berhasil diperbarui.');
        // } catch (\Throwable $th) {
        //     // throw $th;
        //     dd($th->getMessage());
        //     return back()->with('alert', 'danger_Gagal menyimpan');
        // }
        // }
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
        } else {
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
