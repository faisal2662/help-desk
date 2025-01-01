<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    // public function handle(Request $request, Closure $next)
    // {
    //     // // Memproses request ke middleware berikutnya
    //     // $response = $next($request);

    //     // // Menambahkan header keamanan
    //     // // $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self'; style-src 'self';");
    //     // $response->headers->set('X-Frame-Options', 'DENY');
    //     // $response->headers->set('X-Content-Type-Options', 'nosniff');
    //     // $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    //     // $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
    //     // $response->headers->set('Permissions-Policy', 'geolocation=(), camera=(), microphone=()');

    //     // return $response;
    // }
}
