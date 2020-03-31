<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Blacklist\StoreRequest;
use App\Models\Blacklist;
use App\Services\BlacklistService;
use App\Http\Controllers\Controller;

class BlacklistController extends Controller
{
    protected $blacklist;

    public function __construct(BlacklistService $blacklistService)
    {
        $this->blacklist = $blacklistService;
    }

    public function index()
    {
        $blacklists = $this->blacklist->getAll();

        return view('admin.blacklist.index', compact('blacklists'));
    }

    public function add($id)
    {

        return view('admin.blacklist.add');
    }

    public function store(StoreRequest $request)
    {
        Blacklist::create($request->only(['type', 'description', 'user_id']));

        return $this->ajaxReturn(0, '拉黑成功');
    }
}
