<?php

namespace App\Http\Controllers;

use App\Models\NamaPosisi;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use League\CommonMark\Node\Inline\DelimitedInterface;

class NamaPosisiController extends Controller
{

    function __construct()
    {
        $this->role = new RoleAccountController();
        $this->route = "nama_jabatan";
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $role = $this->role->role(auth()->user()->id_pegawai, "", $this->route);

        // dd($role);
        if ($role->can_create == "Y") {
            $route = route('nama_jabatan.create');
            $input = "<a href='" . $route . "'>
							<span class='badge badge-primary'>
							  <i class='bx bx-plus'></i> Tambah Jabatan Baru
							</span>
						</a>";
        } else {
            $input = "";
        }
     
        return view('pages.nama_posisi.index', compact('input'));
    }
    function removeDuplicates($tableName, $columnName)
    {
        // Ambil semua data dari tabel dan kelompokkan berdasarkan kolom yang ditentukan
        $duplicates = DB::table($tableName)
            ->select($columnName, DB::raw('COUNT(*) as count'))
            ->groupBy($columnName)
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            // Ambil semua ID dari duplikat
            $ids = DB::table($tableName)
                ->where($columnName, $duplicate->$columnName)
                ->pluck('id_posisi_pegawai'); // Ganti 'id' dengan nama kolom ID Anda

            // Hapus semua kecuali satu
            $idsToDelete = $ids->slice(1); // Ambil semua ID kecuali yang pertama
            DB::table($tableName)->whereIn('id_posisi_pegawai', $idsToDelete)->delete();
        }

        return response()->json(['message' => 'Data duplikat berhasil dihapus.']);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function datatables()
    {
        $role = $this->role->role(Session::get('id_pegawai'), "", $this->route);
        $namaPosisi = NamaPosisi::where('is_delete', 'N')->get();

        $no = 1;
        foreach ($namaPosisi as $act) {

            $act->no = $no++;
            $route = route('nama_jabatan.edit', $act->id_posisi_pegawai);
            $detail = '<a href="' . route('nama_jabatan.show', $act->id_posisi_pegawai). '"><span class="badge badge-info"><i class="bx bx-search-alt-2"></i> Lihat</span></a>';
            $update_data = $role->can_update == "Y" ? "<a href='$route' class='badge bg-primary text-white'><i class='bx bx-edit'></i> Ubah</a>" : "-";
			$delete_data = $role->can_delete == "Y" ?"<a href='javascript:void(0);' onclick='delete_data($act->id_posisi_pegawai,\"" . $act->nama_posisi . "\")' class='badge bg-danger text-white'><i class='bx bx-trash'></i> Delete</a>": "-";
			$act->action = $detail . "&nbsp;".  $update_data."&nbsp;".$delete_data;

        }
        return DataTables::of($namaPosisi)->escapecolumns([])->make(true);
    }

    public function show($id)
    {
        $namaPosisi = NamaPosisi::where('id_posisi_pegawai', $id)->where('is_delete', 'N')->first();
        if(is_null($namaPosisi)){
            return redirect()->route('nama_jabatan');
        }
        return view('pages.nama_posisi.lihat', compact('namaPosisi'));
    }
    public function create()
    {
        //
        return view('pages.nama_posisi.tambah');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([

            'nama' => 'required',
            'sebagai' => 'required'
        ]);
        try {
            $namaPosisi = new NamaPosisi();
            $namaPosisi->nama_posisi = $request->nama;
            $namaPosisi->sebagai_posisi = $request->sebagai;
            $namaPosisi->created_by = auth()->user()->employee_name;
            $namaPosisi->save();
            return back()->with('alert', 'success_Berhasil disimpan');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('alert', 'danger_gagal menyimpan');
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NamaPosisi  $namaPosisi
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $namaPosisi = NamaPosisi::where('is_delete', 'N')->where('id_posisi_pegawai', $id)->first();

        return view('pages.nama_posisi.ubah', compact('namaPosisi'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NamaPosisi  $namaPosisi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([

            'nama' => 'required',
            'sebagai' => 'required'
        ]);
        try {
            $namaPosisi =  NamaPosisi::where('id_posisi_pegawai', $id)->first();
            $namaPosisi->nama_posisi = $request->nama;
            $namaPosisi->sebagai_posisi = $request->sebagai;
            $namaPosisi->updated_by = auth()->user()->employee_name;
            $namaPosisi->update();
            return back()->with('alert', 'success_Berhasil diperbarui');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('alert', 'danger_gagal diperbarui');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NamaPosisi  $namaPosisi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
        try {
            $namaPosisi =  NamaPosisi::where('id_posisi_pegawai', $request->delete)->first();
            $namaPosisi->deleted_date = date('Y-m-d H:i:s');
            $namaPosisi->is_delete = 'Y';
            $namaPosisi->deleted_by = auth()->user()->employee_name;
            $namaPosisi->update();
            return back()->with('alert', 'success_Berhasil dihapus');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('alert', 'danger_gagal diperbarui');
        }
    }
}
