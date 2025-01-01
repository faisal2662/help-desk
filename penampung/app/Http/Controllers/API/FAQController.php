<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\API\FAQ;

class FAQController extends Controller
{
    public function getFAQs()
    {
        return FAQ::where([
                'delete_faq' => 'N'
            ])
            ->orderBy('urutan_faq', 'ASC')
            ->get();
    }
}
