<?php

namespace App\Http\Controllers\Admin;

use App\Models\Temp;
use App\Models\UmbrellaHistory;
use App\Models\UmbrellaStation;
use App\Models\User;
use App\Repositories\Umbrella\OperationRepository;
use App\Repositories\Umbrella\StationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class DataController extends Controller
{
    public function get_up()
    {

    }

    public function umbrella()
    {
        $station = '深大文科楼';
        $station = UmbrellaStation::where('name', $station)->first();
        $borrow_count = UmbrellaHistory::where('borrow_station', $station->name)->count();
        $still_count = UmbrellaHistory::where('still_station', $station->name)->count();
        $station_count = $station->amount;

        return view('data.umbrella', compact('borrow_count', 'still_count', 'station_count'));
    }

    public function insurance()
    {
        $temps = Temp::where('type', 'shareholder')->get();
        $array = [];
        $temps->each(function ($temp) use (&$array) {
            $temp->data = json_decode($temp->data);
            $array[] = $temp->data;
            return $temp->date;
        });
        $temps = $array;

        return view('data.insurance', compact('temps'));
    }

    public function umbrella_station()
    {
        $stations = StationRepository::self()->closeCache()->getStationsWithNotLendUmbrella();

        return view('data.umbrella_stations', compact('stations'));
    }

    public function umbrella_operation()
    {
        $operation = new OperationRepository();
        $operations[] = ('站点激活的雨伞总数：' . $operation->stationStillUmbrellaCount());
        $operations[] = ('二次使用累计借出雨伞次数：' . $operation->borrowUmbrellasCountNotNull());
        $operations[] = ('累计归还的雨伞次数：' . $operation->stillUmbrellasCount());
        $operations[] = ('总计借出雨伞次数：' . $operation->borrowUmbrellasCount());
        $operations[] = ('雨伞归还率：' . $operation->stillPercent() . '%');
        $operations[] = ('使用人数：' . $operation->umbrellaUsedUsers());
        $operations[] = ('注册人数：' . $operation->umbrellaRegisterUsers());
        $operations[] = ('总计使用过的雨伞数量：' . $operation->usedUmbrellas());
        $operations[] = ('仍在使用的雨伞数量：' . $operation->NotStillUmbrellas());
        $operations[] = ('雨伞丢失率：' . $operation->lostPercent() . '%');
        $operations[] = ('每把雨伞的平均使用次数：' . $operation->umbrellaUseTime());
        $operations[] = ('黑名单人数：' . $operation->blacklists());
        $operations[] = ('运营时间：' . $operation->operationDates() . '天');

        return view('data.umbrella_operation', compact('operations'));
    }
}
