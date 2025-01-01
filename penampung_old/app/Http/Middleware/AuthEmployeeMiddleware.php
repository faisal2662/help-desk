<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

use App\Models\API\Pegawai;
use App\Models\API\PegawaiToken;

class AuthEmployeeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        $employeeToken = PegawaiToken::where([
                'token' => $token,
                'delete_pegawai_token' => 'N'
            ])
            ->first();

        if ($employeeToken == null) {
            return response()->json([
                'message' => 'Token tidak valid'
            ], 404);
        }

        $employee = Pegawai::find($employeeToken->id_pegawai);

        if ($employee == null) {
            return response()->json([
                'message' => 'Pegawai tidak ditemukan'
            ], 404);
        }

        Auth::login($employee);

        return $next($request);
    }
}
