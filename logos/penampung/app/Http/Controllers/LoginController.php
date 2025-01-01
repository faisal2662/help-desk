<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Otp;
use App\Models\Pegawai;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

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

        // $login = $this->login();
        $account = Pegawai::with('NamaPosisi')->where('employee_id', $npp)->where('delete_pegawai', 'N')->where('password', md5($password))->where('status_pegawai', 'Aktif')->first();
        // $account = Pegawai::where('npp_pegawai', $npp)->where('delete_pegawai', 'N')->where('status_pegawai', 'Aktif')->first();
        // if($login['HSTATUS'] == 200){
        if (!$account->NamaPosisi || $account->NamaPosisi->sebagai_posisi == null) {
            return back()->with('alert', 'danger_Gagal masuk silahkan hubungi Admin');
        }
        if ($account) {
            $id_pegawai = $account->id_pegawai;
                $this->generateOtp($id_pegawai);
                return redirect()->route('verify_otp', encrypt($id_pegawai));

        } else {
            return back()->with('alert', 'danger_NPP atau password anda salah.')->withInput($request->all());
        }

        // }
    }

    function logout()
    {
        Auth::logout();
        Session::forget('id_pegawai');
        return redirect()->route('login');
    }

    function verify($id)
    {
        $id = decrypt($id);
        $pegawai = Pegawai::with('NamaPosisi')->where('id_pegawai', $id)->where('delete_pegawai', 'N')->first();
        return view('login.verifyOtp', compact('pegawai'));
    }
    function generateOtp($userId) {
        // Menghasilkan kode OTP acak antara 0 dan 999999
        $otpCode = rand(0, 999999);

        // Memastikan kode OTP selalu 6 digit
        $otpCode = str_pad($otpCode, 6, '0', STR_PAD_LEFT);

        // Simpan OTP ke database
        $pegawai = Pegawai::where('id_pegawai', $userId)->first();
        Otp::create([
            'user_id' => $userId,
            'otp_code' => $otpCode,
            'created_by' => $pegawai->employee_name,
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);



        $emailAddress   = 'amimfaisal2@gmail.com,faisal.drift.3@gmail.com';
        // $emailAddress   = $pegawai->email;
        $subject = 'Helpdesk Kode Masuk';
        $name = $pegawai->employee_name;
        $mail = new PHPMailer(true);                              // Passing true enables exceptions
        try {
            // Pengaturan Server
            //    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = env('MAIL_HOST');                  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = env('MAIL_USERNAME');                 // SMTP username
            $mail->Password = env('MAIL_PASSWORD');                           // SMTP password
            $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, ssl also accepted
            $mail->Port = 465;                                    // TCP port to connect to

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Siapa yang mengirim email
            $mail->setFrom(env('MAIL_FROM_ADDRESS'), 'Helpdesk - Jamkrindo');
            $emails = explode(',', $emailAddress);

            // Tambahkan setiap email ke penerima
            foreach ($emails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $mail->addAddress(trim($email));
                }
                // $mail->addAddress(trim($email)); // trim() untuk menghapus spasi ekstra
            }

            // Siapa yang akan menerima email
            // $mail->addAddress($emailAddress, $name);     // Add a recipient

            // Embedded Image
            $mail->addEmbeddedImage(base_path('../logos/logo.png'), 'logo_cid');

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;

            $mail->Body    = view('login.email_otp', compact('pegawai', 'otpCode'));

            $mail->send();
            // echo 'Message  sent.';
        } catch (Exception $e) {
            // echo 'Message could not be sent.';
            //echo 'Mailer Error: ' . $mail->ErrorInfo;
            // echo $e;
        }
        // Kirim OTP ke pengguna (via SMS atau email)
        // Contoh: Mail::to($user->email)->send(new OtpMail($otpCode));
    }
    function verifyOtp(Request $request) {
        $validator = Validator::make($request->all(), [
            'otp_code' => 'required', // Validasi untuk memastikan 6 digit
        ]);

        if ($validator->fails()) {
            return back()->with('alert', 'danger_kode wajib diisi');
            // return response()->json(['error' => $validator->errors()], 422);
        }
        $id = decrypt($request->id_pegawai);
        $otp = Otp::where('user_id', $id)
                  ->where('otp_code', $request->otp_code)
                  ->where('expires_at', '>', Carbon::now())
                  ->first();
                  $account = Pegawai::where('id_pegawai', $id)->first();
        if ($otp) {
            Session::put('id_pegawai', $id);
            // Session::put('id_pegawai', $account->id_pegawai);
            Auth::guard('web')->login($account);
            // return Auth::check();

            // insert log
            $values = array(
                'id_pegawai' => $account->id_pegawai,
                'tgl_log_pegawai' => date('Y-m-d H:i:s'),
            );
            DB::table('tb_log_pegawai')->insert($values);
            // end insert log

            // return response()->json(['status' => 'login is succesfully']);
            return redirect()->route('dashboard');
            // OTP valid, lanjutkan login
        } else {
            return back()->with('alert', 'danger_Kode OTP tidak valid atau kadaluarsa');
            // OTP tidak valid atau kedaluwarsa
        }
    }
    private function login()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://sf7dev-pro.dataon.com/sfpro/?ofid=sfSystem.loginUser&originapp=hris_jamkrindo',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "USERPWD": "777FF8B018AB23EEE048D13978E0D1FCFF94D326",
            "USERNAME":"jamkrindo",
            "ACCNAME":"jamkrindo",
            "TIMESTAMP": "' . date('Y-m-d H:i:s') . ' +0700"
            }',
            CURLOPT_HTTPHEADER => array(
                'Accept-Language: en-US,en;q=0.9,id;q=0.8',
                'Language: en',
                'Origin: https://workplaze.dataon.com',
                'Referer: https://workplaze.dataon.com/auth',
                'Sec-Fetch-Dest: empty',
                'Sec-Fetch-Mode: cors',
                'User-Agent: Mozilla5.0 (X11; Linux x86_64) AppleWebKit537.36 (KHTML, like Gecko) Chrome125.0.0.0 Safari537.36',
                'sec-ch-ua: "Google Chrome";v="125", "Chromium";v="125", "Not.ABrand";v="24"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Linux"',
                'Content-Type: application/json',
                'Cookie: JSESSIONID=C18D3CB96739FBBB5DD807CBDEB0FA51; LANG=en; _BDC=LANG'
            ),
        ));

        $response = curl_exec($curl);
        $data = json_decode($response, true);

        // dd($data);
        curl_close($curl);
        // echo $response;

        return $data;
    }
}
