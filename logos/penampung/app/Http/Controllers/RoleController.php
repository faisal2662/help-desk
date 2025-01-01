<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

use Auth;
use DataTables;

class RoleController extends Controller
{
    //

    function index()
    {
        return view('role-management.index');
    }

    function datatables()
    {
        $no = 1;
        $account = User::where('is_delete', 'N')->get();

        foreach ($account as $act) {
            $act->no = $no++;
            $act->action = "<a href='" . route('jade.role.detail', $act->kd_user) . "'><i class='bi bi-gear'></i></a>";
        }

        return datatables::of($account)->escapecolumns([])->make(true);
    }

    function detail($id)
    {
        $user = User::where('kd_user', $id)->first();
        $menu = [
            [
                'id_menu' => 1,
                'menu' => 'Dashboard',
                'type' => 'MENU',
            ],
            [
                'id_menu' => 2,
                'menu' => 'User Management',
                'type' => 'MENU',
            ],
            [
                'id_menu' => 3,
                'menu' => 'Customer Management',
                'type' => 'MENU',
            ],
            [
                'id_menu' => 4,
                'menu' => 'Chat Management',
                'type' => 'MENU',
            ],
            [
                'id_menu' => 5,
                'menu' => 'Product',
                'type' => 'SUB_MENU',
            ],
            [
                'id_menu' => 6,
                'menu' => 'Category Product',
                'type' => 'SUB_MENU',
            ],
            [
                'id_menu' => 7,
                'menu' => 'Branch Management',
                'type' => 'SUB_MENU',
            ],
            [
                'id_menu' => 8,
                'menu' => 'Region Management',
                'type' => 'SUB_MENU',
            ],
            [
                'id_menu' => 9,
                'menu' => 'City Management',
                'type' => 'SUB_MENU',
            ],
            [
                'id_menu' => 10,
                'menu' => 'Province Management',
                'type' => 'SUB_MENU',
            ],
            [
                'id_menu' => 11,
                'menu' => 'Master Akses',
                'type' => 'MENU',
            ],
            [
                'id_menu' => 12,
                'menu' => 'Master Data',
                'type' => 'HEADER',
            ],
            [
                'id_menu' => 13,
                'menu' => 'General Options',
                'type' => 'HEADER',
            ],
            [
                'id_menu' => 14,
                'menu' => 'Master Produk',
                'type' => 'MENU',
            ],
            [
                'id_menu' => 15,
                'menu' => 'Master Lokasi',
                'type' => 'MENU',
            ],
            [
                'id_menu' => 16,
                'menu' => 'News Management',
                'type' => 'MENU',
            ],
            [
                'id_menu' => 17,
                'menu' => 'Dashboard Management',
                'type' => 'MENU',
            ],
            [
                'id_menu' => 18,
                'menu' => 'Chat Management',
                'type' => 'MENU',
            ],
        ];

        $arrRole = [];
        $role = Role::where('id_account', $id)->get();

        foreach ($role as $rl) {
            if ($rl->can_access == "Y" && $rl->can_update == "Y" && $rl->can_delete == "Y" && $rl->can_create == "Y" && $rl->can_approve == "Y") {
                $can_all = "Y";
            } else {
                $can_all = "N";
            }

            $arrRole[$rl->id_menu] = [
                'can_access' => $rl->can_access,
                'can_update' => $rl->can_update,
                'can_delete' => $rl->can_delete,
                'can_create' => $rl->can_create,
                'can_all' => $can_all,
                'can_approve' => $rl->can_approve,
            ];
        }

        foreach ($menu as &$mn) { // & untuk referensi array $menu agar nilai bisa diubah
            $mn['can_access'] = isset($arrRole[$mn['id_menu']]['can_access']) ? $arrRole[$mn['id_menu']]['can_access'] : 'N';
            $mn['can_update'] = isset($arrRole[$mn['id_menu']]['can_update']) ? $arrRole[$mn['id_menu']]['can_update'] : 'N';
            $mn['can_delete'] = isset($arrRole[$mn['id_menu']]['can_delete']) ? $arrRole[$mn['id_menu']]['can_delete'] : 'N';
            $mn['can_create'] = isset($arrRole[$mn['id_menu']]['can_create']) ? $arrRole[$mn['id_menu']]['can_create'] : 'N';
            $mn['can_approve'] = isset($arrRole[$mn['id_menu']]['can_approve']) ? $arrRole[$mn['id_menu']]['can_approve'] : 'N';
            $mn['can_all'] = isset($arrRole[$mn['id_menu']]['can_all']) ? $arrRole[$mn['id_menu']]['can_all'] : 'N';
        }

        return view('role-management.detail', compact('user', 'menu'));
    }

    function save(Request $request)
    {
        $idAccount = $request->id_account;
        $menuID = $request->menu_id;
        $can_access = $request->can_access;
        $can_update = $request->can_update;
        $can_create = $request->can_create;
        $can_delete = $request->can_delete;
        $can_approve = $request->can_approve;

        //Delete Role
        Role::where('id_account', $idAccount)->delete();

        for ($i = 0; $i < count($menuID); $i++) {

            $role = new Role;
            $role->id_menu = $menuID[$i];
            $role->id_account = $idAccount;
            $role->can_access = isset($can_access[$menuID[$i]]) ? $can_access[$menuID[$i]] : 'N';
            $role->can_update = isset($can_update[$menuID[$i]]) ? $can_update[$menuID[$i]] : 'N';
            $role->can_delete = isset($can_delete[$menuID[$i]]) ? $can_delete[$menuID[$i]] : 'N';
            $role->can_create = isset($can_create[$menuID[$i]]) ? $can_create[$menuID[$i]] : 'N';
            $role->can_approve = isset($can_approve[$menuID[$i]]) ? $can_approve[$menuID[$i]] : 'N';
            $role->save();
        }

        return redirect()->route('jade.role.detail', $idAccount)->with('alert', 'Hak akses berhasil diubah');
    }
}
