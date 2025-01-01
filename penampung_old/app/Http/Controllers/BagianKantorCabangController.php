<?php

namespace App\Http\Controllers;

use App\Http\Controllers\RoleAccountController;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use DataTables;
use Illuminate\Support\Facades\Session;

class BagianKantorCabangController extends Controller
{

    function __construct()
    {
        $this->role = new RoleAccountController();
        $this->route = "bagian_kantor_cabang";
    }

    public function index()
    {
        $role = $this->role->role(Auth::user()->id_pegawai, "", $this->route);


        if ($role->can_create == "Y") {
            $input = '<a href="?create=data">
								<span class="badge badge-primary">
								  <i class="bx bx-plus"></i> Tambah Bagian Kantor Cabang
								</span>
							</a>';
        } else {
            $input = "";
        }

        return view('pages.bagian_kantor_cabang.index', compact('input'));
    }

    public function datatables(Request $request)
    {
        $role = $this->role->role(Auth::user()->id_pegawai, "", $this->route);

        $bagian_kantor_cabang = DB::table('tb_bagian_kantor_cabang')
            ->join('tb_kantor_cabang', 'tb_kantor_cabang.id_kantor_cabang', '=', 'tb_bagian_kantor_cabang.id_kantor_cabang')
            ->where('tb_bagian_kantor_cabang.delete_bagian_kantor_cabang', '=', 'N')
            ->orderBy('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', 'DESC')
            ->get();

        $no = 1;
        foreach ($bagian_kantor_cabang as $data) {

            $delete = "delete_data(" . $data->id_bagian_kantor_cabang . ", '" . $data->nama_bagian_kantor_cabang . "')";

            $update_data = $role->can_update == "Y" ? '<a href="?update=' . $data->id_bagian_kantor_cabang . '"><span class="badge badge-primary"><i class="bx bx-edit"></i> Ubah</span></a>' : "-";
            $delete_data = $role->can_delete == "Y" ? '<a href="javascript:;" onclick="' . $delete . '"><span class="badge badge-danger"><i class="bx bx-trash"></i> Hapus</span></a>' : "-";

            $data->no = $no++;
            $data->nama_kantor_cabang = $data->nama_kantor_cabang;
            $data->nama_bagian_kantor_cabang = $data->nama_bagian_kantor_cabang;
            $data->action = $update_data . "&nbsp;" . $delete_data;
        }

        return DataTables::of($bagian_kantor_cabang)->escapecolumns([])->make(true);
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
                $nama = $request->nama;
                $values = array(
                    'id_kantor_cabang' => $kantor,
                    'nama_bagian_kantor_cabang' => $nama,
                    'created_by' => $data_pegawai->nama_pegawai,
                    'created_date' => date('Y-m-d H:i:s'),
                );
                DB::table('tb_bagian_kantor_cabang')->insert($values);
                return redirect()->route('bagian_kantor_cabang');
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
                $kantor = $request->kantor;
                $nama = $request->nama;

                $where = array(
                    'id_bagian_kantor_cabang' => $id,
                    'delete_bagian_kantor_cabang' => 'N',
                );
                $values = array(
                    'id_kantor_cabang' => $kantor,
                    'nama_bagian_kantor_cabang' => $nama,
                    'update_by' => $data_pegawai->nama_pegawai,
                    'update_date' => date('Y-m-d H:i:s'),
                );
                DB::table('tb_bagian_kantor_cabang')->where($where)->update($values);
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
                    'id_bagian_kantor_cabang' => $id,
                    'delete_bagian_kantor_cabang' => 'N',
                );
                $values = array(
                    'delete_bagian_kantor_cabang' => 'Y',
                    'delete_by' => $data_pegawai->nama_pegawai,
                    'delete_date' => date('Y-m-d H:i:s'),
                );
                DB::table('tb_bagian_kantor_cabang')->where($where)->update($values);
                return back();
            }
        }
    }
}
