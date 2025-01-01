<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    //
    public function index()
    {
        if (Auth::check()) {
            return redirect('/');
        }

        return view('login.index');
    }

    public function authenticate(Request $request)
    {
        $validateData = $request->validate([
            'npp' => 'required',
            'password' => 'required',
        ]);

        $npp = $request->npp;
        $password = $request->password;

        $account = Pegawai::where('npp_pegawai', $npp)->where('delete_pegawai', 'N')->where('password_pegawai', md5($password))->where('status_pegawai', 'Aktif')->first();

        if ($account) {
            Session::put('id_pegawai', $account->id_pegawai);
            Auth::guard('web')->login($account);
            // return Auth::check();

            // insert log
            $values = array(
                'id_pegawai' => $account->id_pegawai,
                'tgl_log_pegawai' => date('Y-m-d H:i:s'),
            );
            DB::table('tb_log_pegawai')->insert($values);
            // end insert log

            return redirect()->route('dashboard');
        } else {
            return back()->with('alert', 'danger_NPP atau password anda salah.')->withInput($request->all());
        }
    }

    function logout()
    {
        Auth::logout();
        Session::forget('id_pegawai');
        return redirect()->route('login');
    }
}
