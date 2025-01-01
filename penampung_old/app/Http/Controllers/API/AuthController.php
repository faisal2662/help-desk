<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mail;
use Validator;
use App\Models\API\Pegawai;
use App\Models\API\PegawaiToken;

class AuthController extends Controller
{
    public function signIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npp_pegawai' => 'required',
            'password_pegawai' => 'required',
            'sebagai_pegawai' => 'required|in:Mitra/Pelanggan,Agent,Petugas',
            'token_device' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $employee = Pegawai::with([
                'headOfficeSection',
                'regionalOfficeSection',
                'branchOfficeSection',
                'headOffice',
                'regionalOffice',
                'branchOffice',
                'notifications' => function ($query) {
                    $query->orderBy('id_notifikasi', 'DESC');
                },
            ])
            ->where([
                'npp_pegawai' => $request->npp_pegawai,
                'sebagai_pegawai' => $request->sebagai_pegawai
            ])
            ->first();

        if ($employee == null) {
            return response()->json([
                'message' => 'Pegawai tidak terdaftar.'
            ], 404);
        }

        if ($employee->password_pegawai != md5($request->password_pegawai)) {
            return response()->json([
                'message' => 'Password yang Anda masukkan salah.'
            ], 404);
        }

        $token = bcrypt($employee->id_pegawai.'-'.microtime());

        $employeeToken = PegawaiToken::where([
                'id_pegawai' => $employee->id_pegawai,
                'token_device' => $request->token_device,
                'delete_pegawai_token' => 'N'
            ])
            ->first();

        if ($employeeToken == null) {
            PegawaiToken::create([
                'id_pegawai' => $employee->id_pegawai,
                'token' => $token,
                'token_device' => $request->token_device,
                'ip_address' => $this->getClientIP(),
                'tgl_terakhir_aktif' => date('Y-m-d H:i:s'),
                'tgl_pegawai_token' => date('Y-m-d H:i:s')
            ]);
        } else {
            $employeeToken->update([
                'token' => $token,
                'ip_address' => $this->getClientIP(),
                'tgl_terakhir_aktif' => date('Y-m-d H:i:s')
            ]);
        }

        return response()->json([
            'employee' => $employee,
            'token' => $token
        ]);
    }

    public function signOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $employeeToken = PegawaiToken::where([
                'token' => $request->token,
                'delete_pegawai_token' => 'N'
            ])
            ->first();

        if ($employeeToken == null) {
            return response()->json([
                'message' => 'Token tidak terdaftar.'
            ], 404);
        }

        $employeeToken->update([
            'delete_pegawai_token' => 'Y'
        ]);

        return response()->json([
            'message' => 'OK'
        ]);
    }

    public function me(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $employeeToken = PegawaiToken::with([
                'employee.headOfficeSection',
                'employee.regionalOfficeSection',
                'employee.branchOfficeSection',
                'employee.headOffice',
                'employee.regionalOffice',
                'employee.branchOffice',
                'employee.notifications' => function ($query) {
                    $query->orderBy('id_notifikasi', 'DESC');
                },
            ])
            ->where([
                'token' => $request->token,
                'delete_pegawai_token' => 'N'
            ])
            ->first();

        if ($employeeToken == null) {
            return response()->json([
                'message' => 'Token tidak terdaftar.'
            ], 404);
        }

        $employeeToken->update([
            'ip_address' => $this->getClientIP(),
            'tgl_terakhir_aktif' => date('Y-m-d H:i:s')
        ]);

        return $employeeToken->employee;
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npp_pegawai' => 'required',
            'sebagai_pegawai' => 'required|in:Mitra/Pelanggan,Agent,Petugas',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $employee = Pegawai::where([
                'npp_pegawai' => $request->npp_pegawai,
                'sebagai_pegawai' => $request->sebagai_pegawai
            ])
            ->first();

        if ($employee == null) {
            return response()->json([
                'message' => 'Pegawai tidak terdaftar.'
            ], 404);
        }

        Mail::send('masuk.email', ['id' => $employee->id_pegawai], function ($message) use ($employee) {
            $message->to($employee->email_pegawai)
                    ->subject('Lupa Password')
                    ->from('helpdesk@cnplus.id', 'Helpdesk');
        });

        return response()->json([
            'message' => 'Silahkan cek email Anda.'
        ]);
    }

    public function getClientIP() {
        if (getenv('HTTP_CLIENT_IP')) return getenv('HTTP_CLIENT_IP');
        if (getenv('HTTP_X_FORWARDED_FOR')) return getenv('HTTP_X_FORWARDED_FOR');
        if (getenv('HTTP_X_FORWARDED')) return getenv('HTTP_X_FORWARDED');
        if (getenv('HTTP_FORWARDED_FOR')) return getenv('HTTP_FORWARDED_FOR');
        if (getenv('HTTP_FORWARDED')) return getenv('HTTP_FORWARDED');
        if (getenv('REMOTE_ADDR')) return getenv('REMOTE_ADDR');
        return null;
    }
}
