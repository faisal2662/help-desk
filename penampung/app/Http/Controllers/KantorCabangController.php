<?php

namespace App\Http\Controllers;

use DataTables;
use App\Models\Pegawai;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

// use App\Http\Controllers\RoleAccount;

class KantorCabangController extends Controller
{

    function __construct()
    {
        // $this->role = new RoleAccount;
        $this->route = "kantor_cabang";
    }

    public function index()
    {

        // $role = $this->role->role(Session::get('id_pegawai'), "", $this->route);


        // if ($role->can_create == "Y") {
        $input = '<a href="?create=data">
								<span class="badge badge-primary">
								  <i class="bx bx-plus"></i> Tambah Kantor Cabang
								</span>
							</a>';
        // } else {
        //     $input = "";
        // }

        return view('pages.kantor_cabang.index', compact('input'));
    }

    public function datatables(Request $request)
    {
        // $role = $this->role->role(Session::get('id_pegawai'), "", $this->route);

        $kantor_cabang = DB::table('tb_kantor_cabang')
            ->where('tb_kantor_cabang.delete_kantor_cabang', '=', 'N')
            ->orderBy('tb_kantor_cabang.id_kantor_cabang', 'DESC')
            ->get();

        $no = 1;
        foreach ($kantor_cabang as $data) {

            $delete = "delete_data(" . $data->id_kantor_cabang . ", '" . $data->nama_kantor_cabang . "')";
            $detail = '<a href="' . route('kantor_cabang.show', $data->id_kantor_cabang). '"><span class="badge badge-info"><i class="bx bx-search-alt-2"></i> Lihat</span></a>';
            $update_data = '<a href="?update=' . $data->id_kantor_cabang . '"><span class="badge badge-primary"><i class="bx bx-edit"></i> Ubah</span></a>';
            $delete_data = '<a href="javascript:;" onclick="' . $delete . '"><span class="badge badge-danger"><i class="bx bx-trash"></i> Hapus</span></a>';
            // $update_data = $role->can_update == "Y" ? '<a href="?update=' . $data->id_kantor_cabang . '"><span class="badge badge-primary"><i class="bx bx-edit"></i> Ubah</span></a>' : "-";
            // $delete_data = $role->can_delete == "Y" ? '<a href="javascript:;" onclick="' . $delete . '"><span class="badge badge-danger"><i class="bx bx-trash"></i> Hapus</span></a>' : "-";

            $data->no = $no++;
            $data->nama_kantor_cabang = $data->nama_kantor_cabang;
            $data->action = $detail . '&nbsp;' . $update_data . "&nbsp;" . $delete_data;
        }

        return DataTables::of($kantor_cabang)->escapecolumns([])->make(true);
    }

    public function show($id)
    {
        $kantor_cabang = DB::table('tb_kantor_cabang')->where('id_kantor_cabang', $id)->where('delete_kantor_cabang', 'N')->first();
        if (is_null($kantor_cabang)) {
            return redirect()->route('kantor_cabang');
        }
          

        return view('pages.kantor_cabang.lihat', compact('kantor_cabang'));
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
                    'nama_kantor_cabang' => $nama,
                    'created_by' => $data_pegawai->employee_name,
                    'created_date' => date('Y-m-d H:i:s'),
                );
                DB::table('tb_kantor_cabang')->insert($values);
                return redirect()->route('kantor_cabang');
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
                    'id_kantor_cabang' => $id,
                    'delete_kantor_cabang' => 'N',
                );
                $values = array(
                    'nama_kantor_cabang' => $nama,
                    'update_by' => $data_pegawai->employee_name,
                    'update_date' => date('Y-m-d H:i:s'),
                );
                DB::table('tb_kantor_cabang')->where($where)->update($values);
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
                    'id_kantor_cabang' => $id,
                    'delete_kantor_cabang' => 'N',
                );
                $values = array(
                    'delete_kantor_cabang' => 'Y',
                    'delete_by' => $data_pegawai->employee_name,
                    'delete_date' => date('Y-m-d H:i:s'),
                );
                DB::table('tb_kantor_cabang')->where($where)->update($values);
                return back();
            }
        }
    }
}
