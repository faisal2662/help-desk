<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Validator;
use App\Models\API\Pegawai;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_pegawai' => 'required|string',
            'jenkel_pegawai' => 'required|in:Laki-laki,Perempuan',
            'telp_pegawai' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $employee = Auth::user();

        $data = [
            'nama_pegawai' => $request->nama_pegawai,
            'jenkel_pegawai' => $request->jenkel_pegawai,
            'telp_pegawai' => $request->telp_pegawai,
        ];

        $employee->update($data);

        $employee = Pegawai::with([
                'headOfficeSection',
                'regionalOfficeSection',
                'branchOfficeSection'
            ])
            ->find($employee->id_pegawai);

        return response()->json($employee);
    }

    public function changePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'foto_pegawai' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $employee = Auth::user();

        $data = [];

        if ($request->hasFile('foto_pegawai')) {
            $fileName = 'foto_pegawai_'.date('Ymd_His_').'.'.$request->file('foto_pegawai')->getClientOriginalExtension();
            $request->file('foto_pegawai')->move('images', $fileName);
            $data['foto_pegawai'] = url('images/'.$fileName);
        }

        $employee->update($data);

        $employee = Pegawai::with([
                'headOfficeSection',
                'regionalOfficeSection',
                'branchOfficeSection'
            ])
            ->find($employee->id_pegawai);

        return response()->json($employee);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string',
            'confirmation_new_password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if ($request->new_password != $request->confirmation_new_password) {
            return response()->json([
                'message' => 'Konfirmasi Password Baru tidak sesuai dengan Password Baru',
            ], 422);
        }

        $employee = Auth::user();

        if (md5($request->old_password) != $employee->password_pegawai) {
            return response()->json([
                'message' => 'Password Lama tidak sesuai',
            ], 422);
        }

        $data = [
            'password_pegawai' => md5($request->new_password),
        ];

        $employee->update($data);

        return response()->json(['message' => 'OK']);
    }
}
