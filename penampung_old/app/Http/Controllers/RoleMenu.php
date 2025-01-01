<?php

namespace App\Http\Controllers;

use Mail;
use Image;
use Session;
use Socialite;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class RoleMenu extends Controller
{
	function index()
	{
		$no = 1;
		$data['account'] = ["PETUGAS", "MITRA", "AGENT"];
		$role_data = DB::table('tb_role_menu')->where('is_deleted', 'N')->orderBy('position', 'asc')->get();

		foreach($role_data as $rl)
		{
			$data['menu'][$rl->actor][] = [
				'no' => $no++,
				'id_menu' => $rl->id_role_menu,
				'menu' => $rl->menu,
				'actor' => $rl->actor,
				'icon' => $rl->icon,
				'route_name' => $rl->route_name,
				'type' => $rl->type,
				'position' => $rl->position
			];


		}


		return view('pages.role.menu.index', compact('role_data'));
	}

	function save(Request $request)
	{
		$role = DB::table('tb_role_menu')->insert([
			'menu' => $request->menu,
			'route_name' => $request->route_name,
			'icon' => $request->icon,
			'type' => $request->type,
			'position' => $request->position,
			'created_by' =>auth()->user()->id_pegawai,
			'created_date' => date('Y-m-d H:i:s'),
		]);

		return redirect()->route('role.menu');
	}

	function delete($id)
	{
		$role = DB::table('tb_role_menu')->where('id_role_menu', $id)->update([
			'deleted_by' => auth()->user()->id_pegawai,
			'deleted_date' => date('Y-m-d H:i:s'),
			'is_deleted' => 'Y'
		]);

		return redirect()->route('role.menu');
	}

	function detail($id)
	{
		$role = DB::table('tb_role_menu')->where('id_role_menu', $id)->first();
		$data = [
			'id_role_menu' => $role->id_role_menu,
			'menu' => $role->menu,
			'actor' => $role->actor,
			'icon' => $role->icon,
			'route_name' => $role->route_name,
			'type' => $role->type,
			'position' => $role->position
		];

		return response()->json(['status' => 'sucess', 'data' => $data], 200);
	}

	function update($id, Request $request)
	{
		$role = DB::table('tb_role_menu')->where('id_role_menu', $id)
		->update([
			'menu' => $request->menu,
			'route_name' => $request->route_name,
			'icon' => $request->icon,
			'type' => $request->type,
			'updated_by' => auth()->user()->id_pegawai,
			'updated_date' => date('Y-m-d H:i:s'),
			'position' => $request->position
		]);

		return redirect()->route('role.menu');
	}
}
