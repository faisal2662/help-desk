<?php

namespace App\Http\Controllers;

use DataTables;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\RoleAccountController;

class BagianKantorWilayahController extends Controller
{

    function __construct()
    {
        $this->role = new RoleAccountController();
        $this->route = "bagian_kantor_wilayah";
    }

    public function index()
    {
        $role = $this->role->role(Auth::user()->id_pegawai, "", $this->route);


        if ($role->can_create == "Y") {
            $input = '<a href="?create=data">
								<span class="badge badge-primary">
								  <i class="bx bx-plus"></i> Tambah Bagian Kantor Wilayah
								</span>
							</a>';
        } else {
            $input = "";
        }

        return view('pages.bagian_kantor_wilayah.index', compact('input'));
    }

    public function datatables(Request $request)
    {
        $role = $this->role->role(Auth::user()->id_pegawai, "", $this->route);

        $bagian_kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
            ->join('tb_kantor_wilayah', 'tb_kantor_wilayah.id_kantor_wilayah', '=', 'tb_bagian_kantor_wilayah.id_kantor_wilayah')
            ->where('tb_bagian_kantor_wilayah.delete_bagian_kantor_wilayah', '=', 'N')
            ->orderBy('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', 'DESC')
            ->get();

        $no = 1;
        foreach ($bagian_kantor_wilayah as $data) {

            $delete = "delete_data(" . $data->id_bagian_kantor_wilayah . ", '" . $data->nama_bagian_kantor_wilayah . "')";
            $detail = '<a href="' . route('bagian_kantor_wilayah.show', $data->id_bagian_kantor_wilayah). '"><span class="badge badge-info"><i class="bx bx-search-alt-2"></i> Lihat</span></a>';
            $update_data = $role->can_update == "Y" ? '<a href="?update=' . $data->id_bagian_kantor_wilayah . '"><span class="badge badge-primary"><i class="bx bx-edit"></i> Ubah</span></a>' : "-";
            $delete_data = $role->can_delete == "Y" ? '<a href="javascript:;" onclick="' . $delete . '"><span class="badge badge-danger"><i class="bx bx-trash"></i> Hapus</span></a>' : "-";

            $data->no = $no++;
            $data->nama_kantor_wilayah = $data->nama_kantor_wilayah;
            $data->nama_bagian_kantor_wilayah = $data->nama_bagian_kantor_wilayah;
            $data->action = $detail . "&nbsp" . $update_data . "&nbsp;" . $delete_data;
        }

        return DataTables::of($bagian_kantor_wilayah)->escapecolumns([])->make(true);
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
                    'id_kantor_wilayah' => $kantor,
                    'nama_bagian_kantor_wilayah' => $nama,
                    'created_by' => $data_pegawai->employee_name,
                    'created_date' => date('Y-m-d H:i:s'),
                );
                DB::table('tb_bagian_kantor_wilayah')->insert($values);
                return redirect()->route('bagian_kantor_wilayah');
            }
        }
    }
public function show($id)
{
    $bagian_kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', 'tb_kantor_wilayah.id_kantor_wilayah')->where('delete_bagian_kantor_wilayah', 'N')->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', $id)->select('tb_bagian_kantor_wilayah.*', 'tb_kantor_wilayah.nama_kantor_wilayah')->first();
   
    if(is_null($bagian_kantor_wilayah))
    {
        return redirect()->route('bagian_kantor_wilayah');
    }
     return view('pages.bagian_kantor_wilayah.lihat', compact('bagian_kantor_wilayah'));
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
                    'id_bagian_kantor_wilayah' => $id,
                    'delete_bagian_kantor_wilayah' => 'N',
                );
                $values = array(
                    'id_kantor_wilayah' => $kantor,
                    'nama_bagian_kantor_wilayah' => $nama,
                    'update_by' => $data_pegawai->employee_name,
                    'update_date' => date('Y-m-d H:i:s'),
                );
                DB::table('tb_bagian_kantor_wilayah')->where($where)->update($values);
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
                    'id_bagian_kantor_wilayah' => $id,
                    'delete_bagian_kantor_wilayah' => 'N',
                );
                $values = array(
                    'delete_bagian_kantor_wilayah' => 'Y',
                    'delete_by' => $data_pegawai->employee_name,
                    'delete_date' => date('Y-m-d H:i:s'),
                );
                DB::table('tb_bagian_kantor_wilayah')->where($where)->update($values);
                return back();
            }
        }
    }
}
