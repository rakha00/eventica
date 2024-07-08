<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        return view('app.home');
    }

    public function profile()
    {
        return view('app.profile');
    }

    public function eventDetail()
    {
        return view('app.event-detail');
    }
}
