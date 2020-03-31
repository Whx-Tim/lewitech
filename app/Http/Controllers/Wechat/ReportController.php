<?php

namespace App\Http\Controllers\Wechat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function appTest()
    {
        return view('wechat.report.appTest');
    }

    public function storeAppTestSuggest(Request $request)
    {
        $this->validate($request, [
            'content' => 'required'
        ], [
            'content.required' => '请输入建议后提交'
        ]);
        $user = Auth::user();
        $user->reports()->create([
            'type' => 'appTest',
            'data' => $request->input('content')
        ]);

        return $this->ajaxReturn(0, '提交成功，点击查看我提交过的建议查看您的建议');
    }

    public function myReport()
    {
        $user = Auth::user();
        $reports = $user->reports;

        return view('wechat.report.my_report', compact('reports'));
    }
}
