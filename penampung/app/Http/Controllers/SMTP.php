<?php

namespace App\Http\Controllers;


use Mail;
use Image;
use Socialite;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SMTP extends Controller
{

	function __construct(){
		$this->role = new RoleAccountController();
		$this->route = "smtp";

	}

	public function index (){
		$role = $this->role->role(Session::get('id_pegawai'), "", $this->route);
		return view('pages.smtp.index');
	}

	public function save (Request $request){
		$email = 'smtp';
		$host = $request->host;
		$port = preg_replace('/\D/', '', $request->port);
		$username = $request->username;
		$password = $request->password;
		$enkripsi = $request->enkripsi;
		$alamat_email = $request->alamat_email;
		$nama_email = $request->nama_email;

		$values = array(
		  'email_smtp' => $email,
		  'host_smtp' => $host,
		  'port_smtp' => $port,
		  'username_smtp' => $username,
		  'password_smtp' => $password,
		  'enkripsi_smtp' => $enkripsi,
		  'alamat_email_smtp' => $alamat_email,
		  'nama_email_smtp' => $nama_email,
		);
		DB::table('tb_smtp')->insert($values);
		return redirect()->route('smtp')->with('alert', 'success_Berhasil diperbarui.');
	}

	public function update (Request $request){
		$id = $request->update;
		$email = 'smtp';
		$host = $request->host;
		$port = preg_replace('/\D/', '', $request->port);
		$username = $request->username;
		$password = $request->password;
		$enkripsi = $request->enkripsi;
		$alamat_email = $request->alamat_email;
		$nama_email = $request->nama_email;

		$where = array(
		  'id_smtp' => $id,
		  'delete_smtp' => 'N',
		);
		$values = array(
		  'email_smtp' => $email,
		  'host_smtp' => $host,
		  'port_smtp' => $port,
		  'username_smtp' => $username,
		  'password_smtp' => $password,
		  'enkripsi_smtp' => $enkripsi,
		  'alamat_email_smtp' => $alamat_email,
		  'nama_email_smtp' => $nama_email,
		);
		DB::table('tb_smtp')->where($where)->update($values);
		return back()->with('alert', 'success_Berhasil diperbarui.');
	}

}
