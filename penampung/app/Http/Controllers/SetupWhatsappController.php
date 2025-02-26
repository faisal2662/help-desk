<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SetupWhatsapp;

class SetupWhatsappController extends Controller
{
    //
    function __construct()
    {
        $this->role = new RoleAccountController();
        $this->route = "setup_whatsapp";
    }
    public function index()
    {
        $role = $this->role->role(auth()->user()->id_pegawai, "", $this->route);


        $setupWhatsapp = SetupWhatsapp::where('is_delete', 'N')->get();
        foreach ($setupWhatsapp as $item) {
            # code...

            $item->action =  $role->can_update == "Y" ? "<span data-bs-toggle='modal' data-bs-target='#update_setup_whatsapp' style='cursor:pointer;' class='badge bg-info text-white'><i class='bx bx-edit'></i> Ubah</span>" : "-";
        }
        // dd($role);


        return view('pages.setup_whatsapp.index', compact('setupWhatsapp'));
    }

    public function update(Request $request,$id)
    {

        try {
            $setup_whatsapp =  SetupWhatsapp::where('id_setup_whatsapp', $id)->first();
            $setup_whatsapp->status_setup = $request->status_setup;
            $setup_whatsapp->updated_by = auth()->user()->nama_pegawai;
            $setup_whatsapp->update();
            return back()->with('alert', 'success_Berhasil disimpan');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('alert', 'danger_Gagal disimpan');
        }
    }
}
