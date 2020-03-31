<?php

namespace App\Http\Controllers\Admin;

use App\Models\Umbrella;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UmbrellaController extends Controller
{
    public function index()
    {
        $umbrellas = Umbrella::orderByDesc('borrow_at')->paginate();
        $count = Umbrella::where('station_id', '<>', 0)->count();

        return view('admin.umbrella.index', compact('umbrellas', 'count'));
    }
}
