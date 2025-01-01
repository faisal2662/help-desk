<?php

namespace App\Http\Controllers;

use DB;
use Mail;
use Image;
use Session;
use Socialite;
use DataTables;
use Illuminate\Http\Request;

class CPU extends Controller
{

	public function index (){
		return view('pages.cpu.index');
	}

}
