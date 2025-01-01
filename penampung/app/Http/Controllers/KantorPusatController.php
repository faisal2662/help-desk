<?php

namespace App\Http\Controllers;

use DataTables;
use App\Models\Pegawai;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\RoleAccount;
use Illuminate\Support\Facades\Session;

class KantorPusatController extends Controller
{
    function __construct()
    {
        // $this->role = new RoleAccount();
        $this->route = "kantor_pusat";
    }

    public function index()
    {
        $input = '<a href="?create=data">
                            <span class="badge badge-primary">
                              <i class="bx bx-plus"></i> Tambah Kantor Pusat
                            </span>
                        </a>';
        return view('pages.kantor_pusat.index', compact('input'));
    }

    public function datatables(Request $request)
    {
        // $role = $this->role->role(Auth::user()->id_pegawai, "", $this->route);

        $kantor_pusat = DB::table('tb_kantor_pusat')
            ->where('tb_kantor_pusat.delete_kantor_pusat', '=', 'N')
            ->orderBy('tb_kantor_pusat.id_kantor_pusat', 'DESC')
            ->get();

        $no = 1;
        foreach ($kantor_pusat as $data) {

            $delete = "delete_data(" . $data->id_kantor_pusat . ", '" . $data->nama_kantor_pusat . "')";
            $detail = '<a href="' . route('kantor_pusat.show', $data->id_kantor_pusat). '"><span class="badge badge-info"><i class="bx bx-search-alt-2"></i> Lihat</span></a>';
            $update_data = '<a href="?update=' . $data->id_kantor_pusat . '"><span class="badge badge-primary"><i class="bx bx-edit"></i> Ubah</span></a>';
            $delete_data = '<a href="javascript:;" onclick="' . $delete . '"><span class="badge badge-danger"><i class="bx bx-trash"></i> Hapus</span></a>';
            // $update_data = $role->can_update == "Y" ? '<a href="?update=' . $data->id_kantor_pusat . '"><span class="badge badge-primary"><i class="bx bx-edit"></i> Ubah</span></a>' : "-";
            // $delete_data = $role->can_delete == "Y" ? '<a href="javascript:;" onclick="' . $delete . '"><span class="badge badge-danger"><i class="bx bx-trash"></i> Hapus</span></a>' : "-";

            $data->no = $no++;
            $data->nama_kantor_pusat = $data->nama_kantor_pusat;
            $data->action = $detail . "&nbsp;" . $update_data . "&nbsp;" . $delete_data;
        }

        return DataTables::of($kantor_pusat)->escapecolumns([])->make(true);
    }

    public function show($id)
{

    $kantor_pusat = DB::table('tb_kantor_pusat')->where('id_kantor_pusat', $id)->where('delete_kantor_pusat', 'N')->first();
    if(is_null($kantor_pusat)){
        return redirect()->route('kantor_pusat');
    }
    return view('pages.kantor_pusat.lihat', compact('kantor_pusat'));
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

                $nama = $request->nama;
                $values = array(
                    'nama_kantor_pusat' => $nama,
                    'kode_kantor_pusat' => $request->kode,
                    'created_by' => $data_pegawai->employee_name,
                    'created_date' => date('Y-m-d H:i:s'),
                );
                DB::table('tb_kantor_pusat')->insert($values);
                return redirect()->route('kantor_pusat');
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
                $nama = $request->nama;
                $where = array(
                    'id_kantor_pusat' => $id,
                    'delete_kantor_pusat' => 'N',
                );
                $values = array(
                    'nama_kantor_pusat' => $nama,
                    'kode_kantor_pusat' => $request->kode,
                    'update_by' => $data_pegawai->employee_name,
                    'update_date' => date('Y-m-d H:i:s'),
                );
                DB::table('tb_kantor_pusat')->where($where)->update($values);
                return back()->with('alert', 'success_Berhasil diperbarui.');
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
                    'id_kantor_pusat' => $id,
                    'delete_kantor_pusat' => 'N',
                );
                $values = array(
                    'delete_kantor_pusat' => 'Y',
                    'delete_by' => $data_pegawai->employee_name,
                    'delete_date' => date('Y-m-d H:i:s'),
                );
                DB::table('tb_kantor_pusat')->where($where)->update($values);
                return back();
            }
        }
    }
}
