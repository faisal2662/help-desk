<?php

namespace App\Http\Controllers;


use Mail;
use Session;
use Socialite;
use DataTables;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Intervention\Image\Image;
use Illuminate\Support\Facades\DB;

class Profil extends Controller
{

	public function index (){

        $pegawai = Pegawai::with('NamaPosisi')->where('delete_pegawai', 'N')->where('id_pegawai', auth()->user()->id_pegawai)->first();

        if ($pegawai->status_data == 'local') {
            $pegawai->telp_pegawai =  $pegawai->primary_phone;
            $pegawai->email_pegawai = $pegawai->email;
        } else {
            $pegawai->telp_pegawai =  $this->decryptssl($pegawai->primary_phone, 'P/zqOYfEDWHmQ9/g8PrApw==');
            $pegawai->email_pegawai = $this->decryptssl($pegawai->email, 'P/zqOYfEDWHmQ9/g8PrApw==');
        }

        return view('pages.profil.index', compact('pegawai'));
	}

	public function log (Request $request){
		$log = DB::table('tb_log_pegawai')
		->join('tb_pegawai','tb_log_pegawai.id_pegawai','=','tb_pegawai.id_pegawai')
		->where([['tb_log_pegawai.delete_log_pegawai','N'],['tb_log_pegawai.id_pegawai',auth()->user()->id_pegawai]])
		->orderBy('tb_log_pegawai.id_log_pegawai','DESC')
		->get();

		$no = 1;
		foreach($log as $data){

			$data->no = $no++;
			$data->employee_name = $data->employee_name;
			$data->tgl_log_pegawai = date('j F Y, H:i', strtotime($data->tgl_log_pegawai));

		}

		return DataTables::of($log)->escapecolumns([])->make(true);
	}
private function decryptssl($str, $key)
    {
        $str = base64_decode($str);
        $key = base64_decode($key);
        $decrypted = openssl_decrypt($str, 'AES-128-ECB', $key,  OPENSSL_RAW_DATA);
        return $decrypted;
    }
	public function upload (Request $request){
		$id = auth()->user()->id_pegawai;
		$foto = url('logos/avatar.png');
		if(!empty($request->file('foto'))){
		  $file_foto = 'foto_profil_'.date('Ymd_His.').$request->file('foto')->getClientOriginalExtension();
		//   $image_resize = Image::make($request->file('foto')->getRealPath());
		//   $image_resize->fit(250);
		//   $image_resize->save(public_path('../images/' .$file_foto));
        $file  = $request->file('foto');
        $file->move(base_path('../images/'),  $file_foto);
		  $foto = url('images/'.$file_foto);
		}
		$where = array(
		  'id_pegawai' => $id,
		  'delete_pegawai' => 'N',
		);
		$values = array(
		  'foto_pegawai' => $foto,
		);
		DB::table('tb_pegawai')->where($where)->update($values);
		return back()->with('alert', 'success_Profil berhasil diperbarui.');
	}

	public function update (Request $request){
		$nama = $request->nama;
		$jenkel = $request->jenkel;
		$telp = preg_replace('/\D/', '', $request->telp);
		$email = $request->email;

		$pegawai = DB::table('tb_pegawai')
		->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.telp_pegawai', $telp],['tb_pegawai.email_pegawai', $email], ['tb_pegawai.id_pegawai', '!=', auth()->user()->id_pegawai]])
		->get();

		if($pegawai->count() < 1){

			$where = array(
			  'id_pegawai' => auth()->user()->id_pegawai,
			  'delete_pegawai' => 'N',
			);
			$values = array(
			  'nama_pegawai' => $nama,
			  'jenkel_pegawai' => $jenkel,
			  'telp_pegawai' => $telp,
			  'email_pegawai' => $email,
			);
			DB::table('tb_pegawai')->where($where)->update($values);
			return back()->with('alert', 'success_Profil berhasil diperbarui.');

		}else{
			foreach($pegawai as $data_pegawai);
			if($data_pegawai->telp_pegawai == $telp){
				return back()->with('alert', 'danger_No.Telp sudah terdaftar sebelumnya.');
			}else if($data_pegawai->email_pegawai == $email){
				return back()->with('alert', 'danger_Email sudah terdaftar sebelumnya.');
			}
		}
	}

	public function ganti_password (Request $request){
		$pass_lama = md5($request->pass_lama);
		$pass_baru = md5($request->pass_baru);
		$confirm_pass = md5($request->confirm_pass);

		$pegawai = DB::table('tb_pegawai')
		->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.id_pegawai', auth()->user()->id_pegawai],['tb_pegawai.password_pegawai', $pass_lama]])
		->get();

		if($pegawai->count() < 1){
			return back()->with('alert', 'danger_Password lama anda salah.')->withInput($request->all());
		}else{
			if($pass_baru == $confirm_pass){

				$where = array(
				  'id_pegawai' => auth()->user()->id_pegawai,
				  'delete_pegawai' => 'N',
				);
				$values = array(
				  'password_pegawai' => $pass_baru,
				);
				DB::table('tb_pegawai')->where($where)->update($values);

				return back()->with('alert', 'success_Password berhasil diperbarui.');

			}else{
				return back()->with('alert', 'danger_Konfirmasi password baru tidak sesuai.')->withInput($request->all());
			}
		}
	}

}
