<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Session;
use Auth;
use Mail;

class HomeController extends Controller
{
    public function home() {
   



        return view('home');
    }

}