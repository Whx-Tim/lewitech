<?php

namespace App\Http\Controllers\Admin;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BusinessController extends Controller
{
    public function index()
    {
        $businesses = Business::where('status', 1)->get();
        $count = count($businesses);

        return view('admin.business.index', compact('businesses', 'count'));
    }
}
