<?php

namespace App\Http\Controllers;

use App\Repositories\SchoolRepository;
use App\Repositories\Wechat\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        dd(Auth::user());
        return view('welcome');
    }
}
