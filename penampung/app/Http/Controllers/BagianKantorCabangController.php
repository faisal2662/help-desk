<?php

namespace App\Http\Controllers;

use DataTables;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\RoleAccountController;

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
            $detail = '<a href="' . route('bagian_kantor_cabang.show', $data->id_bagian_kantor_cabang). '"><span class="badge badge-info"><i class="bx bx-search-alt-2"></i> Lihat</span></a>';
            $update_data = $role->can_update == "Y" ? '<a href="?update=' . $data->id_bagian_kantor_cabang . '"><span class="badge badge-primary"><i class="bx bx-edit"></i> Ubah</span></a>' : "-";
            $delete_data = $role->can_delete == "Y" ? '<a href="javascript:;" onclick="' . $delete . '"><span class="badge badge-danger"><i class="bx bx-trash"></i> Hapus</span></a>' : "-";

            $data->no = $no++;
            $data->nama_kantor_cabang = $data->nama_kantor_cabang;
            $data->nama_bagian_kantor_cabang = $data->nama_bagian_kantor_cabang;
            $data->action = $detail . '&nbsp;' . $update_data . "&nbsp;" . $delete_data;
        }

        return DataTables::of($bagian_kantor_cabang)->escapecolumns([])->make(true);
    }
 public function show($id)
 {
    $bagian_kantor_cabang = DB::table('tb_bagian_kantor_cabang')->join('tb_kantor_cabang','tb_kantor_cabang.id_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang')->where('id_bagian_kantor_cabang', $id)->where('delete_bagian_kantor_cabang', 'N')->select('tb_bagian_kantor_cabang.*', 'tb_kantor_cabang.nama_kantor_cabang')->first();
if(is_null($bagian_kantor_cabang)){
    return redirect()->route('bagian_kantor_cabang');
}
return view('pages.bagian_kantor_cabang.lihat', compact('bagian_kantor_cabang'));
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
                    'created_by' => $data_pegawai->employee_name,
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
                    'update_by' => $data_pegawai->employee_name,
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
                    'delete_by' => $data_pegawai->employee_name,
                    'delete_date' => date('Y-m-d H:i:s'),
                );
                DB::table('tb_bagian_kantor_cabang')->where($where)->update($values);
                return back();
            }
        }
    }
}
