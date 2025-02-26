<?php

namespace App\Console\Commands;

use App\Models\NamaPosisi;
use App\Models\KantorWilayah;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class apiSunfish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $result = $this->fetchAllData();

        $role_user = ['1', '18', '19', '45', '46', '28', '29', '31', '32', '33', '37', '38', '39', '47', '48', '50'];

        // Ambil semua posisi jabatan dari database (mapping nama ke ID)
        $kantorCabang = DB::table('tb_kantor_cabang')->where('delete_kantor_cabang', 'N')->pluck('id_kantor_cabang', 'nama_kantor_cabang')->toArray();
        $bagianKantorCabang = DB::table('tb_bagian_kantor_cabang')->where('delete_bagian_kantor_cabang', 'N')->get();
        $kantorWilayah = DB::table('tb_kantor_wilayah')
            ->where('delete_kantor_wilayah', 'N')
            ->pluck('id_kantor_wilayah', 'nama_kantor_wilayah')
            ->toArray();

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
                $resultPosisiMapping[strtolower(str_replace(' ', '', $key))] = $value; // Menggunakan nama posisi yang diproses sebagai kunci
            }

            foreach ($result as $pegawai) {
                // $text =   $pegawai['POSITION_NAME'];
                // $text = str_replace("\u00a0", " ", $text);
                // $pegawai['POSITION_NAME'] = $text;

                $posisiPegawai = strtolower(str_replace(' ', '', $pegawai['POSITION_NAME']));

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
                    } else {
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
            }
            return response()->json(['status' => 'success'], 200);
        } catch (\Throwable $th) {
            throw $th;
            die;
            return response()->json(['status' => $th->getMessages]);
            // return response()->json(['status' => 'gagal']);
        }

        echo "Hello World";
        return Command::SUCCESS;
    }

    // public function getApi($result)
    // {


    //     $role_user = ['1', '18', '19', '45', '46', '28', '29', '31', '32', '33', '37', '38', '39', '47', '48', '50'];

    //     // Ambil semua posisi jabatan dari database (mapping nama ke ID)
    //     $kantorCabang = DB::table('tb_kantor_cabang')->where('delete_kantor_cabang', 'N')->pluck('id_kantor_cabang', 'nama_kantor_cabang')->toArray();
    //     $bagianKantorCabang = DB::table('tb_bagian_kantor_cabang')->where('delete_bagian_kantor_cabang', 'N')->get();
    //     $kantorWilayah = DB::table('tb_kantor_wilayah')
    //         ->where('delete_kantor_wilayah', 'N')
    //         ->pluck('id_kantor_wilayah', 'nama_kantor_wilayah')
    //         ->toArray();

    //     // Mengambil data bagian kantor wilayah yang tidak dihapus
    //     $bagianKantorWilayah = DB::table('tb_bagian_kantor_wilayah')
    //         ->where('delete_bagian_kantor_wilayah', 'N') // Memastikan hanya mengambil yang tidak dihapus
    //         ->pluck('id_bagian_kantor_wilayah', 'nama_bagian_kantor_wilayah')
    //         ->toArray();

    //     $idkantorWilayah = KantorWilayah::with('BagianKantorWilayah')
    //         ->where('delete_kantor_wilayah', 'N')->get()->groupBy('nama_kantor_wilayah')->toArray();




    //     $posisiMapping = NamaPosisi::where('is_delete', 'N')->pluck('id_posisi_pegawai', 'nama_posisi')->toArray();
    //     // $kantorPusatNama = DB::table('tb_kantor_pusat')->where('delete_kantor_pusat', 'N')->pluck('id_kantor_pusat', 'nama_kantor_pusat')->toArray();
    //     $kantorPusat = DB::table('tb_kantor_pusat')->where('delete_kantor_pusat', 'N')->pluck('id_kantor_pusat', 'nama_kantor_pusat')->toArray();
    //     $bagianKantorPusat = DB::table('tb_bagian_kantor_pusat')->where('delete_bagian_kantor_pusat', 'N')->pluck('id_bagian_kantor_pusat', 'nama_bagian_kantor_pusat')->toArray();
    //     $processedBagianKantorPusat = []; // Array untuk menyimpan hasil yang diproses
    //     $processedBagianKantorWilayah = []; // Array untuk menyimpan hasil yang

    //     try {

    //         foreach ($bagianKantorPusat as $key => $value) {
    //             foreach ($kantorPusat as $kantorKey => $kantorValue) {
    //                 // Menggabungkan nama bagian dengan nama kantor pusat
    //                 $bagian_kantor_pusat = $key . ' ' . $kantorKey; // Menggabungkan dengan spasi

    //                 $bagian_kantor_pusat = strtolower(str_replace(' ', '', $bagian_kantor_pusat));
    //                 // Simpan hasil yang telah diproses, termasuk ID bagian dan ID kantor pusat
    //                 $processedBagianKantorPusat[$bagian_kantor_pusat] = [
    //                     'id_bagian' => $value, // ID bagian
    //                     'id_kantor' => $kantorValue, // ID kantor pusat
    //                 ];
    //             }
    //         }
    //         // Mengambil data kantor wilayah yang tidak dihapus

    //         // Inisialisasi array untuk menyimpan hasil yang diproses
    //         $processedBagianKantorWilayah = [];

    //         // Menggabungkan nama bagian dengan nama kantor wilayah
    //         foreach ($bagianKantorWilayah as $key => $value) {
    //             foreach ($kantorWilayah as $kantorKey => $kantorValue) {
    //                 // Menggabungkan nama bagian dengan nama kantor wilayah
    //                 $bagian_kantor_wilayah = $key . ' ' . $kantorKey; // Menggabungkan dengan spasi

    //                 // Simpan hasil yang telah diproses, termasuk ID bagian dan ID kantor
    //                 $processedBagianKantorWilayah[$bagian_kantor_wilayah] = [
    //                     'id_bagian' => $value, // ID bagian
    //                     'id_kantor' => $kantorValue, // ID kantor wilayah
    //                 ];
    //             }
    //         }


    //         // Membuat array kunci dari posisi yang telah diproses
    //         $resultPosisiMapping = [];
    //         foreach ($posisiMapping as $key => $value) {
    //             $resultPosisiMapping[strtolower(str_replace(' ', '', $key))] = $value; // Menggunakan nama posisi yang diproses sebagai kunci
    //         }

    //         foreach ($result as $pegawai) {
    //             // $text =   $pegawai['POSITION_NAME'];
    //             // $text = str_replace("\u00a0", " ", $text);
    //             // $pegawai['POSITION_NAME'] = $text;
    //             $pegawai['id_bagian_kantor_pusat'] = 0;
    //             $pegawai['id_bagian_kantor_cabang'] = 0;
    //             $pegawai['id_bagian_kantor_wilayah'] = 0;
    //             $posisiPegawai = strtolower(str_replace(' ', '', $pegawai['POSITION_NAME']));

    //             $data_pegawai = DB::table('tb_pegawai')->where('delete_pegawai', 'N')->where('employee_id', $pegawai['EMPLOYEE_ID'])->first();
    //             if (!is_null($data_pegawai)) {
    //                 // Update data pegawai jika sudah ada
    //                 $pegawai['created_by'] = 'api-sunfish';
    //                 $pegawai['created_date'] = now(); // Menggunakan now() untuk mendapatkan timestamp saat ini
    //                 DB::table('tb_pegawai')->where('delete_pegawai', 'N')->where('employee_id', $pegawai['EMPLOYEE_ID'])->update($pegawai);
    //                 $get_data_pegawai = DB::table('tb_pegawai')->where('employee_id', $pegawai['EMPLOYEE_ID'])->where('id_bagian_kantor_pusat', 0)->where('id_bagian_kantor_cabang', 0)->where('id_bagian_kantor_wilayah', 0)->first();

    //                 $get_kepala_unit_kerja  = DB::table('tb_kepala_unit_kerja')->where('delete_kepala_unit_kerja', 'N')->where('id_pegawai', $get_data_pegawai->id_pegawai)->first();
    //                 if (!is_null($get_data_pegawai) && !is_null($get_kepala_unit_kerja)) {

    //                     $prosesDepartmentPegawai = strtolower(str_replace(' ', '', $pegawai['DEPARTMENT_NAME']));
    //                     if (array_key_exists($posisiPegawai, $resultPosisiMapping)) {
    //                         $pegawai['id_posisi_pegawai'] = $resultPosisiMapping[$posisiPegawai];
    //                     } else {
    //                         $pegawai['id_posisi_pegawai'] = null;
    //                     }


    //                     // Menyimpan data berdasarkan kondisi
    //                     if ($pegawai['BRANCH_NAME'] == 'Kantor Pusat') {
    //                         // dd($pegawai) ;
    //                         if ($pegawai['EMPLOYEE_ID'] == '00880') {
    //                             return response()->json($prosesDepartmentPegawai);
    //                         }
    //                         if (array_key_exists($prosesDepartmentPegawai,  $processedBagianKantorPusat)) {
    //                             //  return $processedBagianKantorPusat ;

    //                             $kepala = 'Kepala ' . $pegawai['DIVISION_NAME'];
    //                             $pegawai['id_bagian_kantor_pusat'] = $processedBagianKantorPusat[$prosesDepartmentPegawai]['id_bagian'];
    //                             $idKantorPusat = $processedBagianKantorPusat[$prosesDepartmentPegawai]['id_kantor'];
    //                             $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];
    //                             // return $pegawai
    //                             $get_pegawai = DB::table('tb_pegawai')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update($pegawai);

    //                             if ($pegawai['POSITION_NAME'] == $kepala) {

    //                                 $kepala_unit = DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
    //                                 if ($kepala_unit) {
    //                                     DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update([
    //                                         'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                         'id_bagian_kantor_pusat' => $idKantorPusat,
    //                                         'id_bagian_kantor_wilayah' => 0,
    //                                         'id_bagian_kantor_cabang' => 0,
    //                                     ]);
    //                                 } else {
    //                                     DB::table('tb_kepala_unit_kerja')->insert([
    //                                         'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                         'id_pegawai' => $get_data_pegawai->id_pegawai,
    //                                         'id_bagian_kantor_pusat' => $idKantorPusat,
    //                                         'id_bagian_kantor_cabang' => 0,
    //                                         'id_bagian_kantor_wilayah' => 0,
    //                                     ]);
    //                                 }
    //                             }
    //                         } elseif (array_key_exists($pegawai['DEPARTMENT_NAME'], $kantorCabang)) {
    //                             $kepala = 'Pimpinan ' . $pegawai['DIVISION_NAME'];

    //                             $idKantorCabang = $kantorCabang[$pegawai['DEPARTMENT_NAME']];
    //                             $bagianKantorCabang = DB::table('tb_bagian_kantor_cabang')->where('delete_bagian_kantor_cabang', 'N')->where('id_kantor_cabang', $idKantorCabang)->pluck('id_bagian_kantor_cabang', 'nama_bagian_kantor_cabang')->toArray();

    //                             $sectionName = preg_replace('/KC .*/', '', $pegawai['SECTION_NAME']);
    //                             $beforeSectionName = trim($sectionName);
    //                             $sectionName = 'Bagian ' . $beforeSectionName;
    //                             if (array_key_exists($sectionName, $bagianKantorCabang)) {

    //                                 $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$sectionName];
    //                             } else if (strpos($beforeSectionName, 'Bagian') !== 'Bagian') {
    //                                 $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$beforeSectionName];
    //                             } else {

    //                                 $pegawai['id_bagian_kantor_cabang'] = 0;
    //                             }
    //                             // $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$sectionName] ? $bagianKantorCabang[$sectionName] : 0;
    //                             $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];

    //                             $get_pegawai = DB::table('tb_pegawai')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update($pegawai);
    //                             if ($pegawai['POSITION_NAME'] == $kepala) {
    //                                 $kepala_unit =   DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
    //                                 if ($kepala_unit) {
    //                                     DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update([
    //                                         'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                         'id_bagian_kantor_cabang' => $idKantorCabang,
    //                                         'id_bagian_kantor_pusat' => 0,
    //                                         'id_bagian_kantor_wilayah' => 0,
    //                                     ]);
    //                                 } else {
    //                                     DB::table('tb_kepala_unit_kerja')->insert([
    //                                         'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                         'id_bagian_kantor_cabang' => $idKantorCabang,
    //                                         'id_pegawai' => $get_data_pegawai->id_pegawai,
    //                                         'id_bagian_kantor_pusat' => 0,
    //                                         'id_bagian_kantor_wilayah' => 0,
    //                                     ]);
    //                                 }
    //                             }
    //                         }
    //                     } elseif (array_key_exists($pegawai['DEPARTMENT_NAME'], $kantorWilayah)) {
    //                         $kepala = 'Pimpinan ' . $pegawai['DIVISION_NAME'];

    //                         $idKantorWilayah = $kantorWilayah[$pegawai['DEPARTMENT_NAME']];
    //                         $bagianKantorWilayah = DB::table('tb_bagian_kantor_wilayah')->where('delete_bagian_kantor_wilayah', 'N')->where('id_kantor_cabang', $idKantorWilayah)->pluck('id_bagian_kantor_wilayah', 'nama_bagian_kantor_wilayah')->toArray();

    //                         $sectionName = preg_replace('/KC .*/', '', $pegawai['SECTION_NAME']);
    //                         $beforeSectionName = trim($sectionName);
    //                         $sectionName = 'Bagian ' . $beforeSectionName;
    //                         if (array_key_exists($sectionName, $bagianKantorWilayah)) {

    //                             $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$sectionName];
    //                         } else if (strpos($beforeSectionName, 'Bagian')) {
    //                             $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$beforeSectionName];
    //                         } else {

    //                             $pegawai['id_bagian_kantor_wilayah'] = 0;
    //                         }
    //                         // $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$sectionName] ? $bagianKantorWilaah[$sectionName] : 0;
    //                         $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];


    //                         $get_pegawai = DB::table('tb_pegawai')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update($pegawai);

    //                         if ($pegawai['POSITION_NAME'] == $kepala) {
    //                             $kepala_unit =   DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
    //                             if ($kepala_unit) {
    //                                 DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update([
    //                                     'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                     'id_bagian_kantor_wilayah' => $idKantorWilayah,
    //                                     'id_bagian_kantor_cabang' => 0,
    //                                     'id_bagian_kantor_pusat' => 0,
    //                                 ]);
    //                             } else {
    //                                 DB::table('tb_kepala_unit_kerja')->insert([
    //                                     'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                     'id_pegawai' => $get_data_pegawai->id_pegawai,
    //                                     'id_bagian_kantor_wilayah' => $idKantorWilayah,
    //                                     'id_bagian_kantor_cabang' => 0,
    //                                     'id_bagian_kantor_pusat' => 0,
    //                                 ]);
    //                             }
    //                         }
    //                     } else  if (array_key_exists($pegawai['DEPARTMENT_NAME'], $kantorPusat)) {

    //                         if (array_key_exists($pegawai['DEPARTMENT_NAME'], $kantorPusat)) {
    //                             $kepala = 'Kepala ' . $pegawai['DIVISION_NAME'];
    //                             $idKantorPusat = $kantorPusat[$pegawai['DIVISION_NAME']];
    //                             // $idBagianKantorPusat = DB::table('tb_bagian_kantor_pusat')->where('id_kantor_pusat', $idKantorPusat)->where('nama_bagian_kantor_pusat', $pegawai['se']);
    //                             $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];
    //                             // return $pegawai;
    //                             $get_pegawai = DB::table('tb_pegawai')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update($pegawai);
    //                             $idKantorPusat = $kantorPusat[$pegawai['DIVISION_NAME']];
    //                             // if ($pegawai['POSITION_NAME'] == $kepala) {

    //                             $kepala_unit = DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
    //                             if ($kepala_unit) {
    //                                 DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update([
    //                                     'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                     'id_bagian_kantor_pusat' => $idKantorPusat,
    //                                     'id_bagian_kantor_wilayah' => 0,
    //                                     'id_bagian_kantor_cabang' => 0,
    //                                 ]);
    //                             } else {
    //                                 DB::table('tb_kepala_unit_kerja')->insert([
    //                                     'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                     'id_pegawai' => $get_data_pegawai->id_pegawai,
    //                                     'id_bagian_kantor_pusat' => $idKantorPusat,
    //                                     'id_bagian_kantor_cabang' => 0,
    //                                     'id_bagian_kantor_wilayah' => 0,
    //                                 ]);
    //                             }
    //                         } else {
    //                             $idKantorPusat = 0;
    //                         }
    //                     } else if (array_key_exists($pegawai['DEPARTMENT_NAME'], $bagianKantorPusat)) {
    //                         if (strpos($pegawai['BRANCH_NAME'], 'Kantor Unit Pelayanan') !== false) {
    //                             $posisi = str_replace('Kantor Unit Pelayanan', '', $pegawai['BRANCH_NAME']);
    //                             $kepala = 'Kepala KUP' . $posisi;
    //                             $sectionName = rtrim($pegawai['SECTION_NAME']);
    //                             if (isset($kantorCabang[$sectionName])) {
    //                                 $idKantorCabang = $kantorCabang[$pegawai['SECTION_NAME']];
    //                             } else {

    //                                 $idKantorCabang = 0;
    //                             }
    //                             $pegawai['id_bagian_kantor_cabang'] = $idKantorCabang;
    //                             $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];
    //                             $get_pegawai = DB::table('tb_pegawai')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update($pegawai);
    //                             for ($i = 0; $i < count($role_user); $i++) {
    //                                 DB::table('tb_role_user')->insert([
    //                                     'id_account' => $get_pegawai,
    //                                     'id_role_menu' => $role_user[$i],
    //                                     'can_access' => "Y",
    //                                     'can_create' => "N",
    //                                     'can_update' => "N",
    //                                     'can_delete' => "N",
    //                                     'flag' => 'GENERATE',

    //                                 ]);
    //                             }
    //                             // $data[] = $pegawai;
    //                             if ($pegawai['POSITION_NAME'] == $kepala) {

    //                                 DB::table('tb_kepala_unit_kerja')->insert([
    //                                     'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                     'id_pegawai' => $get_pegawai,
    //                                     'id_bagian_kantor_cabang' => $idKantorCabang,
    //                                     'id_bagian_kantor_pusat' => 0,
    //                                     'id_bagian_kantor_wilayah' => 0,
    //                                 ]);
    //                             }
    //                         } else {

    //                             $kepala = 'Kepala ' . $pegawai['DIVISION_NAME'];
    //                             $pegawai['id_bagian_kantor_pusat'] = $bagianKantorPusat[$pegawai['DEPARTMENT_NAME']];
    //                             $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];
    //                             $get_pegawai = DB::table('tb_pegawai')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update($pegawai);
    //                             if (isset($kantorPusat[$pegawai['DIVISION_NAME']])) {

    //                                 $idKantorPusat = $kantorPusat[$pegawai['DIVISION_NAME']];
    //                             } else {
    //                                 return response()->json($pegawai);
    //                                 $idKantorPusat = 0;
    //                             }
    //                             if ($pegawai['POSITION_NAME'] == $kepala) {

    //                                 $kepala_unit = DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
    //                                 if ($kepala_unit) {
    //                                     DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update([
    //                                         'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                         'id_bagian_kantor_pusat' => $idKantorPusat,
    //                                         'id_bagian_kantor_wilayah' => 0,
    //                                         'id_bagian_kantor_cabang' => 0,
    //                                     ]);
    //                                 } else {
    //                                     DB::table('tb_kepala_unit_kerja')->insert([
    //                                         'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                         'id_pegawai' => $get_data_pegawai->id_pegawai,
    //                                         'id_bagian_kantor_pusat' => $idKantorPusat,
    //                                         'id_bagian_kantor_cabang' => 0,
    //                                         'id_bagian_kantor_wilayah' => 0,
    //                                     ]);
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 } elseif (array_key_exists($pegawai['BRANCH_NAME'], $kantorCabang)) {
    //                     $kepala = 'Pemimpin ' . $pegawai['BRANCH_NAME'];

    //                     $idKantorCabang = $kantorCabang[$pegawai['BRANCH_NAME']];
    //                     $bagianKantorCabang = DB::table('tb_bagian_kantor_cabang')->where('delete_bagian_kantor_cabang', 'N')->where('id_kantor_cabang', $idKantorCabang)->pluck('id_bagian_kantor_cabang', 'nama_bagian_kantor_cabang')->toArray();
    //                     $sectionName = preg_replace('/KC .*/', '', $pegawai['SECTION_NAME']);
    //                     $beforeSectionName = trim($sectionName);
    //                     $sectionName = 'Bagian ' . $beforeSectionName;
    //                     if (array_key_exists($sectionName, $bagianKantorCabang)) {

    //                         $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$sectionName];
    //                     } else if (strpos($beforeSectionName, 'Bagian') !== false) {
    //                         if (isset($bagianKantorCabang[$beforeSectionName])) {

    //                             $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$beforeSectionName];
    //                         }
    //                         if (strpos($beforeSectionName, 'KCK') !== false) {
    //                             $sectionName = preg_replace('/\S*KCK.*/', '', $beforeSectionName);
    //                             $beforeSectionName = trim($sectionName);
    //                             // dd($pegawai);
    //                             if (isset($bagianKantorCabang[$beforeSectionName])) {

    //                                 $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$beforeSectionName];
    //                             }
    //                         }
    //                     } else {

    //                         $pegawai['id_bagian_kantor_cabang'] = 0;
    //                     }
    //                     $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];

    //                     $get_pegawai = DB::table('tb_pegawai')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update($pegawai);

    //                     // $data[] = $pegawai;
    //                     if ($pegawai['POSITION_NAME'] == $kepala) {

    //                         $kepala_unit = DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
    //                         if ($kepala_unit) {
    //                             DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update([
    //                                 'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                 'id_bagian_kantor_cabang' => $idKantorCabang,
    //                                 'id_bagian_kantor_pusat' => 0,
    //                                 'id_bagian_kantor_wilayah' => 0,
    //                             ]);
    //                         } else {
    //                             DB::table('tb_kepala_unit_kerja')->insert([
    //                                 'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                 'id_bagian_kantor_cabang' => $idKantorCabang,
    //                                 'id_pegawai' => $get_data_pegawai->id_pegawai,
    //                                 'id_bagian_kantor_pusat' => 0,
    //                                 'id_bagian_kantor_wilayah' => 0,
    //                             ]);
    //                         }
    //                     }
    //                 } elseif (array_key_exists($pegawai['BRANCH_NAME'], $kantorWilayah)) {
    //                     $kepala = 'Pimpinan ' . $pegawai['DIVISION_NAME'];

    //                     $idKantorWilayah = $kantorWilayah[$pegawai['BRANCH_NAME']];
    //                     $bagianKantorWilayah = DB::table('tb_bagian_kantor_wilayah')->where('delete_bagian_kantor_wilayah', 'N')->where('id_kantor_wilayah', $idKantorWilayah)->pluck('id_bagian_kantor_wilayah', 'nama_bagian_kantor_wilayah')->toArray();
    //                     $sectionName = preg_replace('/KW .*/', '', $pegawai['SECTION_NAME']);
    //                     $beforeSectionName = trim($sectionName);

    //                     $sectionName = 'Bagian ' . $beforeSectionName;
    //                     if (array_key_exists($sectionName, $bagianKantorWilayah)) {

    //                         $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$sectionName];
    //                     } else if (strpos($beforeSectionName, 'Bagian') !== false) {
    //                         if (isset($bagianKantorWilayah[$beforeSectionName])) {

    //                             $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$beforeSectionName];
    //                         }
    //                         if (isset($processedBagianKantorWilayah[$beforeSectionName]['id_bagian'])) {

    //                             $pegawai['id_bagian_kantor_wilayah'] = $processedBagianKantorWilayah[$beforeSectionName]['id_bagian'];
    //                         }
    //                     } else {

    //                         $pegawai['id_bagian_kantor_wilayah'] = 0;
    //                     }
    //                     // $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$sectionName] ? $bagianKantorWilayah[$sectionName] :0;
    //                     $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];

    //                     $get_pegawai = DB::table('tb_pegawai')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update($pegawai);
    //                     if ($pegawai['POSITION_NAME'] == $kepala) {
    //                         $kepala_unit =   DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->where('delete_kepala_unit_kerja', 'N')->first();
    //                         if ($kepala_unit) {
    //                             DB::table('tb_kepala_unit_kerja')->where('id_pegawai', $get_data_pegawai->id_pegawai)->update([
    //                                 'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                 'id_bagian_kantor_wilayah' => $idKantorWilayah,
    //                                 'id_bagian_kantor_cabang' => 0,
    //                                 'id_bagian_kantor_pusat' => 0,
    //                             ]);
    //                         } else {
    //                             DB::table('tb_kepala_unit_kerja')->insert([
    //                                 'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                 'id_pegawai' => $get_data_pegawai->id_pegawai,
    //                                 'id_bagian_kantor_wilayah' => $idKantorWilayah,
    //                                 'id_bagian_kantor_cabang' => 0,
    //                                 'id_bagian_kantor_pusat' => 0,
    //                             ]);
    //                         }
    //                     }
    //                 }
    //             } else {
    //                 // Menyimpan data pegawai baru

    //                 $pegawai['sebagai_pegawai'] = "Staff";
    //                 $pegawai['status_pegawai'] = "Aktif";
    //                 $pegawai['created_date'] = now();
    //                 $pegawai['created_by'] = 'api-sunfish';

    //                 // Menentukan ID posisi pegawai
    //                 $prosesDepartmentPegawai = strtolower(str_replace(' ', '', $pegawai['DEPARTMENT_NAME']));
    //                 if (array_key_exists($posisiPegawai, $resultPosisiMapping)) {
    //                     $pegawai['id_posisi_pegawai'] = $resultPosisiMapping[$posisiPegawai];
    //                 } else {
    //                     $pegawai['id_posisi_pegawai'] = null;
    //                 }


    //                 // Menyimpan data berdasarkan kondisi
    //                 if ($pegawai['BRANCH_NAME'] == 'Kantor Pusat') {
    //                     // dd($pegawai) ;
    //                     if (array_key_exists($prosesDepartmentPegawai,  $processedBagianKantorPusat)) {
    //                         //  return $processedBagianKantorPusat ;

    //                         $kepala = 'Kepala ' . $pegawai['DIVISION_NAME'];
    //                         $pegawai['id_bagian_kantor_pusat'] = $processedBagianKantorPusat[$prosesDepartmentPegawai]['id_bagian'];
    //                         $idKantorPusat = $processedBagianKantorPusat[$prosesDepartmentPegawai]['id_kantor'];
    //                         $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];
    //                         // return $pegawai;
    //                         $get_pegawai = DB::table('tb_pegawai')->insertGetId($pegawai);
    //                         for ($i = 0; $i < count($role_user); $i++) {
    //                             DB::table('tb_role_user')->insert([
    //                                 'id_account' => $get_pegawai,
    //                                 'id_role_menu' => $role_user[$i],
    //                                 'can_access' => "Y",
    //                                 'can_create' => "N",
    //                                 'can_update' => "N",
    //                                 'can_delete' => "N",

    //                             ]);
    //                         }
    //                         if ($pegawai['POSITION_NAME'] == $kepala) {
    //                             DB::table('tb_kepala_unit_kerja')->insert([
    //                                 'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                 'id_pegawai' => $get_pegawai,
    //                                 'id_bagian_kantor_pusat' => $idKantorPusat,
    //                                 'id_bagian_kantor_pusat' => 0,
    //                                 'id_bagian_kantor_wilayah' => 0,
    //                             ]);
    //                         }
    //                     } elseif (array_key_exists($pegawai['DEPARTMENT_NAME'], $kantorCabang)) {
    //                         $kepala = 'Pimpinan ' . $pegawai['DIVISION_NAME'];

    //                         $idKantorCabang = $kantorCabang[$pegawai['DEPARTMENT_NAME']];
    //                         $bagianKantorCabang = DB::table('tb_bagian_kantor_cabang')->where('delete_bagian_kantor_cabang', 'N')->where('id_kantor_cabang', $idKantorCabang)->pluck('id_bagian_kantor_cabang', 'nama_bagian_kantor_cabang')->toArray();

    //                         $sectionName = preg_replace('/KC .*/', '', $pegawai['SECTION_NAME']);
    //                         $beforeSectionName = trim($sectionName);
    //                         $sectionName = 'Bagian ' . $beforeSectionName;
    //                         if (array_key_exists($sectionName, $bagianKantorCabang)) {

    //                             $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$sectionName];
    //                         } else if (strpos($beforeSectionName, 'Bagian') !== 'Bagian') {
    //                             $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$beforeSectionName];
    //                         } else {

    //                             $pegawai['id_bagian_kantor_cabang'] = 0;
    //                         }
    //                         // $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$sectionName] ? $bagianKantorCabang[$sectionName] : 0;
    //                         $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];

    //                         $get_pegawai = DB::table('tb_pegawai')->insertGetId($pegawai);
    //                         for ($i = 0; $i < count($role_user); $i++) {
    //                             DB::table('tb_role_user')->insert([
    //                                 'id_account' => $get_pegawai,
    //                                 'id_role_menu' => $role_user[$i],
    //                                 'can_access' => "Y",
    //                                 'can_create' => "N",
    //                                 'can_update' => "N",
    //                                 'can_delete' => "N",
    //                                 'flag' => 'GENERATE',

    //                             ]);
    //                         }
    //                         if ($pegawai['POSITION_NAME'] == $kepala) {
    //                             DB::table('tb_kepala_unit_kerja')->insert([
    //                                 'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                 'id_pegawai' => $get_pegawai,
    //                                 'id_bagian_kantor_cabang' => $idKantorCabang,
    //                                 'id_bagian_kantor_pusat' => 0,
    //                                 'id_bagian_kantor_wilayah' => 0,
    //                             ]);
    //                         }
    //                     } elseif (array_key_exists($pegawai['DEPARTMENT_NAME'], $kantorWilayah)) {
    //                         $kepala = 'Pimpinan ' . $pegawai['DIVISION_NAME'];

    //                         $idKantorWilayah = $kantorWilayah[$pegawai['DEPARTMENT_NAME']];
    //                         $bagianKantorWilayah = DB::table('tb_bagian_kantor_wilayah')->where('delete_bagian_kantor_wilayah', 'N')->where('id_kantor_cabang', $idKantorWilayah)->pluck('id_bagian_kantor_wilayah', 'nama_bagian_kantor_wilayah')->toArray();

    //                         $sectionName = preg_replace('/KC .*/', '', $pegawai['SECTION_NAME']);
    //                         $beforeSectionName = trim($sectionName);
    //                         $sectionName = 'Bagian ' . $beforeSectionName;
    //                         if (array_key_exists($sectionName, $bagianKantorWilayah)) {

    //                             $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$sectionName];
    //                         } else if (strpos($beforeSectionName, 'Bagian')) {
    //                             $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$beforeSectionName];
    //                         } else {

    //                             $pegawai['id_bagian_kantor_wilayah'] = 0;
    //                         }
    //                         // $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$sectionName] ? $bagianKantorWilaah[$sectionName] : 0;
    //                         $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];


    //                         $get_pegawai = DB::table('tb_pegawai')->insertGetId($pegawai);
    //                         for ($i = 0; $i < count($role_user); $i++) {
    //                             DB::table('tb_role_user')->insert([
    //                                 'id_account' => $get_pegawai,
    //                                 'id_role_menu' => $role_user[$i],
    //                                 'can_access' => "Y",
    //                                 'can_create' => "N",
    //                                 'can_update' => "N",
    //                                 'can_delete' => "N",
    //                                 'flag' => 'GENERATE',


    //                             ]);
    //                         }
    //                         if ($pegawai['POSITION_NAME'] == $kepala) {
    //                             DB::table('tb_kepala_unit_kerja')->insert([
    //                                 'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                 'id_pegawai' => $get_pegawai,
    //                                 'id_bagian_kantor_wilayah' => $idKantorWilayah,
    //                                 'id_bagian_kantor_pusat' => 0,
    //                                 'id_bagian_kantor_cabang' => 0,
    //                             ]);
    //                         }
    //                     } else  if (array_key_exists($pegawai['DEPARTMENT_NAME'], $kantorPusat)) {

    //                         if (array_key_exists($pegawai['DEPARTMENT_NAME'], $kantorPusat)) {
    //                             $kepala = 'Kepala ' . $pegawai['DIVISION_NAME'];



    //                             $idKantorPusat = $kantorPusat[$pegawai['DIVISION_NAME']];
    //                             // $idBagianKantorPusat = DB::table('tb_bagian_kantor_pusat')->where('id_kantor_pusat', $idKantorPusat)->where('nama_bagian_kantor_pusat', $pegawai['se']);
    //                             $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];
    //                             // return $pegawai;
    //                             $get_pegawai = DB::table('tb_pegawai')->insertGetId($pegawai);
    //                             for ($i = 0; $i < count($role_user); $i++) {
    //                                 DB::table('tb_role_user')->insert([
    //                                     'id_account' => $get_pegawai,
    //                                     'id_role_menu' => $role_user[$i],
    //                                     'can_access' => "Y",
    //                                     'can_create' => "N",
    //                                     'can_update' => "N",
    //                                     'can_delete' => "N",
    //                                     'flag' => 'GENERATE',

    //                                 ]);
    //                             }
    //                             $idKantorPusat = $kantorPusat[$pegawai['DIVISION_NAME']];
    //                             if ($pegawai['POSITION_NAME'] == $kepala) {
    //                                 DB::table('tb_kepala_unit_kerja')->insert([
    //                                     'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                     'id_pegawai' => $get_pegawai,
    //                                     'id_bagian_kantor_pusat' => $idKantorPusat,
    //                                     'id_bagian_kantor_cabang' => 0,
    //                                     'id_bagian_kantor_wilayah' => 0,
    //                                 ]);
    //                             }
    //                             // return $pegawai;



    //                         } else {
    //                             $idKantorPusat = 0;
    //                         }
    //                     } else if (array_key_exists($pegawai['DEPARTMENT_NAME'], $bagianKantorPusat)) {

    //                         $kepala = 'Kepala ' . $pegawai['DIVISION_NAME'];
    //                         $pegawai['id_bagian_kantor_pusat'] = $bagianKantorPusat[$pegawai['DEPARTMENT_NAME']];
    //                         $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];
    //                         $get_pegawai = DB::table('tb_pegawai')->insertGetId($pegawai);
    //                         for ($i = 0; $i < count($role_user); $i++) {
    //                             DB::table('tb_role_user')->insert([
    //                                 'id_account' => $get_pegawai,
    //                                 'id_role_menu' => $role_user[$i],
    //                                 'can_access' => "Y",
    //                                 'can_create' => "N",
    //                                 'can_update' => "N",
    //                                 'can_delete' => "N",
    //                                 'flag' => 'GENERATE',

    //                             ]);
    //                         }
    //                         // dd($pegawai);
    //                         if (isset($kantoPusat[$pegawai['DIVISION_NAME']])) {
    //                             $idKantorPusat = $kantorPusat[$pegawai['DIVISION_NAME']];
    //                         } else {

    //                             $idKantorPusat = 0;
    //                         }
    //                         if ($pegawai['POSITION_NAME'] == $kepala) {
    //                             DB::table('tb_kepala_unit_kerja')->insert([
    //                                 'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                 'id_pegawai' => $get_pegawai,
    //                                 'id_bagian_kantor_pusat' => $idKantorPusat,
    //                                 'id_bagian_kantor_cabang' => 0,
    //                                 'id_bagian_kantor_wilayah' => 0,
    //                             ]);
    //                         }
    //                     }
    //                 } elseif (array_key_exists($pegawai['BRANCH_NAME'], $kantorCabang)) {
    //                     if (strpos($pegawai['BRANCH_NAME'], 'Kantor Unit Pelayanan') !== false) {
    //                         $posisi = str_replace('Kantor Unit Pelayanan', '', $pegawai['BRANCH_NAME']);
    //                         $kepala = 'Kepala KUP' . $posisi;
    //                         $sectionName = rtrim($pegawai['SECTION_NAME']);
    //                         if (isset($kantorCabang[$sectionName])) {
    //                             $idKantorCabang = $kantorCabang[$pegawai['SECTION_NAME']];
    //                         } else {

    //                             $idKantorCabang = 0;
    //                         }
    //                         $pegawai['id_bagian_kantor_cabang'] = $idKantorCabang;
    //                         $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];

    //                         $get_pegawai = DB::table('tb_pegawai')->insertGetId($pegawai);
    //                         for ($i = 0; $i < count($role_user); $i++) {
    //                             DB::table('tb_role_user')->insert([
    //                                 'id_account' => $get_pegawai,
    //                                 'id_role_menu' => $role_user[$i],
    //                                 'can_access' => "Y",
    //                                 'can_create' => "N",
    //                                 'can_update' => "N",
    //                                 'can_delete' => "N",
    //                                 'flag' => 'GENERATE',

    //                             ]);
    //                         }
    //                         // $data[] = $pegawai;
    //                         if ($pegawai['POSITION_NAME'] == $kepala) {

    //                             DB::table('tb_kepala_unit_kerja')->insert([
    //                                 'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                 'id_pegawai' => $get_pegawai,
    //                                 'id_bagian_kantor_cabang' => $idKantorCabang,
    //                                 'id_bagian_kantor_pusat' => 0,
    //                                 'id_bagian_kantor_wilayah' => 0,
    //                             ]);
    //                         }
    //                     } else {
    //                         $kepala = 'Pemimpin ' . $pegawai['BRANCH_NAME'];

    //                         $idKantorCabang = $kantorCabang[$pegawai['BRANCH_NAME']];
    //                         $bagianKantorCabang = DB::table('tb_bagian_kantor_cabang')->where('delete_bagian_kantor_cabang', 'N')->where('id_kantor_cabang', $idKantorCabang)->pluck('id_bagian_kantor_cabang', 'nama_bagian_kantor_cabang')->toArray();
    //                         $sectionName = preg_replace('/KC .*/', '', $pegawai['SECTION_NAME']);
    //                         $beforeSectionName = trim($sectionName);
    //                         $sectionName = 'Bagian ' . $beforeSectionName;
    //                         if (array_key_exists($sectionName, $bagianKantorCabang)) {

    //                             $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$sectionName];
    //                         } else if (strpos($beforeSectionName, 'Bagian') !== false) {
    //                             if (isset($bagianKantorCabang[$beforeSectionName])) {

    //                                 $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$beforeSectionName];
    //                             }
    //                             if (strpos($beforeSectionName, 'KCK') !== false) {
    //                                 $sectionName = preg_replace('/\S*KCK.*/', '', $beforeSectionName);
    //                                 $beforeSectionName = trim($sectionName);
    //                                 // dd($pegawai);
    //                                 if (isset($bagianKantorCabang[$beforeSectionName])) {

    //                                     $pegawai['id_bagian_kantor_cabang'] = $bagianKantorCabang[$beforeSectionName];
    //                                 }
    //                             }
    //                         } else {

    //                             $pegawai['id_bagian_kantor_cabang'] = 0;
    //                         }
    //                         $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];

    //                         $get_pegawai = DB::table('tb_pegawai')->insertGetId($pegawai);
    //                         for ($i = 0; $i < count($role_user); $i++) {
    //                             DB::table('tb_role_user')->insert([
    //                                 'id_account' => $get_pegawai,
    //                                 'id_role_menu' => $role_user[$i],
    //                                 'can_access' => "Y",
    //                                 'can_create' => "N",
    //                                 'can_update' => "N",
    //                                 'can_delete' => "N",
    //                                 'flag' => 'GENERATE',

    //                             ]);
    //                         }
    //                         $data[] = $pegawai;
    //                         if ($pegawai['POSITION_NAME'] == $kepala) {
    //                             DB::table('tb_kepala_unit_kerja')->insert([
    //                                 'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                                 'id_pegawai' => $get_pegawai,
    //                                 'id_bagian_kantor_cabang' => $idKantorCabang,
    //                                 'id_bagian_kantor_pusat' => 0,
    //                                 'id_bagian_kantor_wilayah' => 0,
    //                             ]);
    //                         }
    //                     }
    //                 } elseif (array_key_exists($pegawai['BRANCH_NAME'], $kantorWilayah)) {
    //                     $kepala = 'Pimpinan ' . $pegawai['DIVISION_NAME'];

    //                     $idKantorWilayah = $kantorWilayah[$pegawai['BRANCH_NAME']];
    //                     $bagianKantorWilayah = DB::table('tb_bagian_kantor_wilayah')->where('delete_bagian_kantor_wilayah', 'N')->where('id_kantor_wilayah', $idKantorWilayah)->pluck('id_bagian_kantor_wilayah', 'nama_bagian_kantor_wilayah')->toArray();
    //                     $sectionName = preg_replace('/KW .*/', '', $pegawai['SECTION_NAME']);
    //                     $beforeSectionName = trim($sectionName);

    //                     $sectionName = 'Bagian ' . $beforeSectionName;
    //                     if (array_key_exists($sectionName, $bagianKantorWilayah)) {

    //                         $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$sectionName];
    //                     } else if (strpos($beforeSectionName, 'Bagian') !== false) {
    //                         if (isset($bagianKantorWilayah[$beforeSectionName])) {

    //                             $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$beforeSectionName];
    //                         }
    //                         if (isset($processedBagianKantorWilayah[$beforeSectionName]['id_bagian'])) {

    //                             $pegawai['id_bagian_kantor_wilayah'] = $processedBagianKantorWilayah[$beforeSectionName]['id_bagian'];
    //                         }
    //                     } else {

    //                         $pegawai['id_bagian_kantor_wilayah'] = 0;
    //                     }
    //                     // $pegawai['id_bagian_kantor_wilayah'] = $bagianKantorWilayah[$sectionName] ? $bagianKantorWilayah[$sectionName] :0;
    //                     $pegawai['kantor_pegawai'] = $pegawai['BRANCH_NAME'];

    //                     $get_pegawai = DB::table('tb_pegawai')->insertGetId($pegawai);
    //                     for ($i = 0; $i < count($role_user); $i++) {
    //                         DB::table('tb_role_user')->insert([
    //                             'id_account' => $get_pegawai,
    //                             'id_role_menu' => $role_user[$i],
    //                             'can_access' => "Y",
    //                             'can_create' => "N",
    //                             'can_update' => "N",
    //                             'can_delete' => "N",
    //                             'flag' => 'GENERATE',

    //                         ]);
    //                     }
    //                     if ($pegawai['POSITION_NAME'] == $kepala) {
    //                         DB::table('tb_kepala_unit_kerja')->insert([
    //                             'kantor_pegawai' => $pegawai['BRANCH_NAME'],
    //                             'id_pegawai' => $get_pegawai,
    //                             'id_bagian_kantor_wilayah' => $idKantorWilayah,
    //                             'id_bagian_kantor_cabang' => 0,
    //                             'id_bagian_kantor_pusat' => 0,
    //                         ]);
    //                     }
    //                 }
    //             }
    //         }
    //         // return response()->json(['status' => 'success'], 200);
    //     } catch (\Throwable $th) {
    //         throw $th;
    //         die;
    //         return response()->json(['status' => $th->getMessages]);
    //         // return response()->json(['status' => 'gagal']);
    //     }
    // }
    private function login()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://sf7dev-pro.dataon.com/sfpro/?ofid=sfSystem.loginUser&originapp=hris_jamkrindo',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "USERPWD": "777FF8B018AB23EEE048D13978E0D1FCFF94D326",
            "USERNAME":"jamkrindo",
            "ACCNAME":"jamkrindo",
            "TIMESTAMP": "' . date('Y-m-d H:i:s') . ' +0700"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        $data = json_decode($response, true);

        curl_close($curl);
        // echo $response;

        return $data;
    }
    private function fetchData($page)
    {

        $token = $this->login();

        $token = $token['DATA']['JWT_TOKEN'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://sf7dev-pro.dataon.com/sfpro/?qlid=HrisUser.getEmployee',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                    "page_number" : "' . $page . '"
                }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
                'Cookie: JSESSIONID=1D874707C6E28326AA74FA53FF48D8CE; LANG=en; _BDC=JSESSIONID,LANG'
            ),
        ));

        $response = curl_exec($curl);
        $data = json_decode($response, true);
        // $data = $data['DATA'];


        curl_close($curl);
        // echo $response;

        return $data;
    }
    // Fungsi untuk mengambil semua data dari setiap halaman
    private function fetchAllData()
    {
        $allData = [];
        $currentPage = 1;
        $perPage = 10;
        $totalPages = 1;

        do {
            // Ambil data dari API
            $response = $this->fetchData($currentPage);

            if (!$response) {
                return false; // Jika ada error, return false
            }

            // Gabungkan data dari halaman saat ini ke allData
            $allData = array_merge($allData, $response['DATA']['DATA']);

            // Hitung total halaman
            $totalPages = ceil($response['DATA']['TOTAL'] / $perPage);

            $currentPage++;
        } while ($currentPage <= $totalPages);

        return $allData;
    }
}
