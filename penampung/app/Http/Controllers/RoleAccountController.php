<?php

namespace App\Http\Controllers;

use Mail;
use Session;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleAccountController extends Controller
{
    function index()
    {

        $pelanggan = DB::table('tb_pegawai')->where('sebagai_pegawai', 'Mitra/Pelanggan')->where('delete_pegawai', 'N')->get();
        $petugas = DB::table('tb_pegawai')->where('sebagai_pegawai', 'Petugas')->where('delete_pegawai', 'N')->get();
        $agent = DB::table('tb_pegawai')->where('sebagai_pegawai', 'Agent')->where('delete_pegawai', 'N')->get();

        $data['account'] = ["PETUGAS", "MITRA", "AGENT"];

        $data['MITRA'] = array();
        $data['PETUGAS'] = array();
        $data['AGENT'] = array();

        foreach ($petugas as $pet) {
            $data['PETUGAS'][] = [
                'id' => $pet->id_pegawai,
                'name' => $pet->nama_pegawai,
                'telp' => $pet->telp_pegawai,
                'email' => $pet->email_pegawai
            ];
        }

        foreach ($pelanggan as $pel) {
            $data['MITRA'][] = [
                'id' => $pel->id_pegawai,
                'name' => $pel->nama_pegawai,
                'telp' => $pel->telp_pegawai,
                'email' => $pel->email_pegawai,
            ];
        }

        foreach ($agent as $at) {
            $data['AGENT'][] = [
                'id' => $at->id_pegawai,
                'name' => $at->nama_pegawai,
                'telp' => $at->telp_pegawai,
                'email' => $at->email_pegawai
            ];
        }

        return view('pages.role.account.index', compact('data'));
    }

    function datatables()
    {
        $pegawai = DB::table('tb_pegawai')
            ->where('delete_pegawai', 'N')
            ->orderBy('created_date', 'desc')
            ->select('id_pegawai','employee_id', 'employee_name', 'gender', 'sebagai_pegawai','position_name', 'status_pegawai', 'kantor_pegawai')
            ->get();
        $no = 1;

        foreach ($pegawai as $peg) {

            $peg->action = "<a href=" . route('role.account.setting', $peg->id_pegawai) . " style='text-decoration: none;'><i class='bx bx-brightness'></i> Setting</a>&nbsp;";
            $peg->no = $no++;
        }

        return datatables::of($pegawai)->escapecolumns([])->make(true);
    }

    function setting($id)
    {
        $data['role'] = array();

        $role_menu = DB::table('tb_role_menu')
            // ->where('actor', $actor)
            ->where('tb_role_menu.is_deleted', 'N')
            ->orderBy('position', 'asc')
            ->get();

        $user = DB::table('tb_pegawai')->where('id_pegawai', $id)->first();

        $role_user = DB::table('tb_role_user')->where('id_account', $id)->get();

        foreach ($role_user as $rs) {
            $data['role'][$rs->id_role_menu][$rs->id_account] = [
                'can_access' => $rs->can_access,
                'can_create' => $rs->can_create,
                'can_update' => $rs->can_update,
                'can_delete' => $rs->can_delete
            ];
        }



        foreach ($role_menu as $rm) {
            $rm->can_access = isset($data['role'][$rm->id_role_menu][$id]['can_access']) ? $data['role'][$rm->id_role_menu][$id]['can_access'] : "N";
            $rm->can_create = isset($data['role'][$rm->id_role_menu][$id]['can_create']) ? $data['role'][$rm->id_role_menu][$id]['can_create'] : "N";
            $rm->can_update = isset($data['role'][$rm->id_role_menu][$id]['can_update']) ? $data['role'][$rm->id_role_menu][$id]['can_update'] : "N";
            $rm->can_delete = isset($data['role'][$rm->id_role_menu][$id]['can_delete']) ? $data['role'][$rm->id_role_menu][$id]['can_delete'] : "N";
        }

        return view('pages.role.account.setting', compact('role_menu', 'user'));
    }

    function updateSetting(Request $request)
    {
        DB::table('tb_role_user')->where('id_account', $request->id_account[0])->delete();

        for ($i = 0; $i < count($request->id_menu); $i++) {
            DB::table('tb_role_user')->insert([
                'id_account' => $request->id_account[$i],
                'id_role_menu' => $request->id_menu[$i],
                'actor' => $request->actor[$i],
                'can_access' => isset($_REQUEST['access-' . $request->id_menu[$i]]) ? "Y" : "N",
                'can_create' => isset($_REQUEST['input-' . $request->id_menu[$i]]) ? "Y" : "N",
                'can_update' => isset($_REQUEST['update-' . $request->id_menu[$i]]) ? "Y" : "N",
                'can_delete' => isset($_REQUEST['delete-' . $request->id_menu[$i]]) ? "Y" : "N",

            ]);
        }
        return redirect()->route('role.account.setting', [$request->id_account[0], $request->actor[0]]);
    }

    public function role($id, $actor, $route)
    {
        $role = DB::table('tb_role_menu')
            ->leftjoin('tb_role_user', 'tb_role_menu.id_role_menu', 'tb_role_user.id_role_menu')
            ->where('route_name', $route)
            ->where('tb_role_user.id_account', $id)
            ->first();

        return $role;
    }
}
