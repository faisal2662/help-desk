<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mail;

class VersionController extends Controller
{
    public function getVersion(Request $request)
    {
        return [
            'version' => '1.0.0',
            'build_number' => 1,
            'ignore' => true,
            'title' => 'Update Aplikasi',
            'content' => 'Update aplikasi sekarang.',
            'url' => 'https://jamkrindo.co.id/',
        ];
    }

    public function sendMail()
    {
        Mail::send('pengaduan.email_pending', ['id_pengaduan' => 5], function ($message) {
            $message->to('ulumcyber@gmail.com')
                    ->subject('Pengaduan Baru (Pending)')
                    ->from('helpdesk@cnplus.id', 'Helpdesk');
        });
    }
}
