<?php

namespace App\Http\Controllers\Web;

use App\Models\Config;
use App\Models\Sign;
use App\Models\SignTimerApply;
use App\Models\Temp;
use App\Models\User;
use App\Repositories\SignDealRepository;
use App\Repositories\SignInfoRepository;
use App\Repositories\SignMedalRepository;
use App\Repositories\SignRepository;
use App\Repositories\SignTimerRepository;
use App\Services\ImageService;
use App\Services\SignService;
use App\Services\WeatherService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;

class IndexController extends Controller
{
    public function index()
    {
        return view('web.index');
    }

    public function introduction()
    {
        return view('web.introduction');
    }

    public function app()
    {
        return view('web.app');
    }

    public function sign()
    {
        return redirect()->route('web.index');
        return view('web.sign');
    }

    public function umbrella()
    {
        return view('web.umbrella');
    }

    public function test(SignInfoRepository $signInfoRepository, SignService $signService)
    {
        dd($signInfoRepository->free2depositCanApply());
        $free_applies = User::whereHas('signApplies', function ($query) {
            $query->where('timer_id', 3)->where('is_free', 1);
        })->get();
        $users = User::whereHas('sign_info', function ($query) {
            $query->where('month_count', '>=', 7)->where('status', SignInfoRepository::NORMAL_SIGN);
        })->get();
        $success_users = [];
        foreach ($free_applies as $apply) {
            foreach ($users as $key => $user) {
                if ($apply->id == $user->id) {
                    $success_users []= $user;
                    unset($user);
                }
            }
        }
        dd($success_users);

    }
}
