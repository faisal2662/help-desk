<?php

namespace App\Http\Controllers;

use DataTables;

use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
    //

    private $role;
    private $route;

    function __construct()
    {
        $this->role = new RoleAccountController();
        $this->route = "hari_libur";
    }

    public function index()
    {
        $role = $this->role->role(Auth::user()->id_pegawai, "", $this->route);


        if ($role->can_create == "Y") {
            $route = route('hari_libur.create');
            $input = '<a href='.$route. '>
								<span class="badge badge-primary">
								  <i class="bx bx-plus"></i> Tambah hari Libur
								</span>
							</a>';
        } else {
            $input = "";
        }
        return view('pages.hari_libur.index', compact('input'));
    }

    public function create()
    {
        return view('pages.hari_libur.tambah');
    }

    public function save(Request $request)
    {
        $request->validate([
            'tanggal' => 'required',
            'nama' => 'required'
        ], [
            'tanggal.required' => 'Tanggal Wajib diisi..',
            'nama.required' => 'Nama Wajib diisi'
        ]);

        try {
            $hari_libur = new Holiday();
            $hari_libur->nama_hari_libur = $request->nama;
            $hari_libur->keterangan_hari_libur = $request->keterangan;
            $hari_libur->tanggal = $request->tanggal;
            $hari_libur->created_by = auth()->user()->nama_pegawai;
            $hari_libur->save();

            return back()->with('alert', 'success_Berhasil disimpan');
        } catch (\Throwable $th) {
            return back('alert', 'danger_gagal harap diisi yang wajib');
            //throw $th;
        }
    }

    public function datatables(Request $request)
    {
        $role = $this->role->role(Auth::user()->id_pegawai, "", $this->route);

        $hari_libur = Holiday::where('is_delete', 'N')->orderBy('tanggal', 'asc')->get();

        $no = 1;
        foreach ($hari_libur as $value) {
            # code...
            $value->no = $no++;
            $delete = "delete_data(" . $value->id_hari_libur . ", '" . $value->nama_hari_libur . "')";

            $update_data = $role->can_update == "Y" ? '<a href="' . route('hari_libur.update',$value->id_hari_libur) . '"><span class="badge badge-primary"><i class="bx bx-edit"></i> Ubah</span></a>' : "-";
            $delete_data = $role->can_delete == "Y" ? '<a href="javascript:;" onclick="' . $delete . '"><span class="badge badge-danger"><i class="bx bx-trash"></i> Hapus</span></a>' : "-";
            $value->tanggal = Carbon::parse($value->tanggal)->translatedFormat('l, j F Y');
            $value->action = $update_data . "&nbsp;" . $delete_data;

        }
        return DataTables::of($hari_libur)->escapecolumns([])->make(true);

    }

    public function update($id)
    {
        $hari_libur = Holiday::where('id_hari_libur', $id)->first();


        return view('pages.hari_libur.ubah', compact('hari_libur'));

    }

    public function edit($id, Request $request)
    {
        try {
            $hari_libur = Holiday::where('id_hari_libur', $id)->first();
            $hari_libur->tanggal = $request->tanggal;
            $hari_libur->nama_hari_libur = $request->nama;
            $hari_libur->keterangan_hari_libur = $request->keterangan;
            $hari_libur->updated_by = auth()->user()->nama_pegawai;
            $hari_libur->update();

            return back()->with('alert', 'success_Berhasil diperbarui');
        } catch (\Throwable $th) {
            return back()->with('alert', 'danger_gagal harap diisi yang wajib');
            //throw $th;
        }
    }

    public function delete(Request $request)
    {

        $id = $request->delete;
        try {
            $hari_libur = Holiday::where('id_hari_libur', $id)->first();
            $hari_libur->deleted_by = auth()->user()->nama_pegawai;
            $hari_libur->deleted_date = date('Y-m-d H:i:s');
            $hari_libur->is_delete = 'Y';
            $hari_libur->update();

            return back()->with('alert', 'success_Berhasil dihapus');
        } catch (\Throwable $th) {
          
            return back()->with('alert', 'danger_gagal harap diisi yang wajib');
            //throw $th;
        }
    }
}


