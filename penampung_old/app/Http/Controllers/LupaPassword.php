<?php

namespace App\Http\Controllers;

use DB;
use Mail;
use Image;
use Session;
use Socialite;
use DataTables;
use Illuminate\Http\Request;

class LupaPassword extends Controller
{

	public function index (){
		return view('lupa_password.index');
	}
	
	public function update (Request $request){
		$role = $request->role;
		$email = $request->email;
		$password = md5($request->password);
		
		$pegawai = DB::table('tb_pegawai')
		->where('tb_pegawai.delete_pegawai','=','N')
		->where('tb_pegawai.status_pegawai','=','Aktif')
		->whereRaw('md5(tb_pegawai.email_pegawai) = ? ', [$email])
		->whereRaw('md5(tb_pegawai.sebagai_pegawai) = ? ', [$role])
		->get();
		
		if($pegawai->count() < 1){
			return back();
		}else{
			foreach($pegawai as $data_pegawai);
			$where = array(
			  'id_pegawai' => $data_pegawai->id_pegawai,
			  'delete_pegawai' => 'N',
			);
			$values = array(
			  'password_pegawai' => $password,
			);
			DB::table('tb_pegawai')->where($where)->update($values);
			return redirect()->route('masuk')->with('alert', 'success_Password berhasil diperbarui.');
		}
	}

}