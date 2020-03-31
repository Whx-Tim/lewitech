<?php
namespace App\Presenters;

use App\Models\SignInfo;
use App\Models\User;
use App\Repositories\SignCardRepository;
use App\Repositories\SignInfoRepository;
use App\Repositories\SignRepository;
use App\Repositories\SignTimerRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SignPresenter
{
    public function signDateBox()
    {
        $now = Carbon::now();
        $start_month = Carbon::now()->startOfMonth();

        $month_day = Carbon::now()->daysInMonth;
        $month_header_week = $start_month->dayOfWeek;
        $month_end_week = Carbon::now()->endOfMonth()->dayOfWeek;
        $total_day = $month_header_week + $month_day;
        $week = 1;
        $html = '<tr week="'. $week .'">';
        $signed_array = $this->user_signed_date();
        $start_date = Carbon::now()->startOfMonth();
        for ($i = 0; $i < $total_day; $i++) {
            if ($i < $month_header_week) {
                $html .= '<td>&nbsp;</td>';
                continue;
            }
            $sign_status = false;
            foreach ($signed_array as $key => $item) {

                if ($start_date->toDateString() == $item['time']) {
                    if ($item['status']) {
                        $sign_status = true;
                    }
                    unset($signed_array[$key]);
                    break;
                }
            }
            if ($sign_status) {
                if ($i%7 == 6) {
                    $html .= '<td class="active">'. ($i+1-$month_header_week) .'</td></tr><tr week="'. ++$week .'">';
                }else {
                    $html .= '<td class="active">'. ($i+1-$month_header_week) .'</td>';
                }
            } else {
                if ($i%7 == 6) {
                    $html .= '<td>'. ($i+1-$month_header_week) .'</td></tr><tr week="'. ++$week .'">';
                }else {
                    $html .= '<td>'. ($i+1-$month_header_week) .'</td>';
                }
            }
            $start_date->addDay();

        }
        for ($i = 0; $i < 7; $i++) {
            if ($i > $month_end_week) {
                $html .= '<td>&nbsp;</td>';
            }
        }
        $html .= '</tr>';

        echo $html;
    }

    public function now_week()
    {
        echo Carbon::now()->weekOfMonth;
    }

    private function user_signed_date()
    {
        $user = Auth::user();
        $start_month = Carbon::now()->startOfMonth()->toDateString();
        $now = Carbon::now()->toDateString();
        $signed_array = $user->signs()->whereDate('today_time', '>=', $start_month)->whereDate('today_time', '<=', $now)->where('today_status', '<>', SignRepository::LOST_SIGN)->get();
        $array = [];
        foreach ($signed_array as $key => $item) {
            $array[] = ['time' => Carbon::parse($item->today_time)->toDateString(), 'status' => $item->today_status];
        }

        return $array;
    }

    public function today_time($timestamp)
    {
        if (empty($timestamp)) {
            echo '尚未签到';
        } else {
            try {
                echo Carbon::parse($timestamp)->format('H:i');
            }catch (\Exception $exception) {
                echo '';
            }
        }
    }

    public function applyStatus2string($status)
    {
        if ($status) {
            echo '已报名';
        } else {
            echo '未报名';
        }
    }

    public function applyStatus()
    {
        $openTimer = SignTimerRepository::static_getOpeningTimer();
        $applyTimer = SignTimerRepository::static_getApplyingTimer();
        $sign_info_repository = new SignInfoRepository(new SignInfo());
        if ($applyTimer) {
            if ($sign_info_repository->isApplyInTimer($applyTimer)) {
                return '已报名';
            } else {
                return '未报名';
            }
        } else {
            if ($sign_info_repository->isApplyInTimer($openTimer)) {
                return '已报名';
            } else {
                return '未报名';
            }
        }
    }

    public function value_rank($value)
    {
        if ($value) {
            echo '第'.$value.'名';
        } else {
            echo '尚未排名';
        }
    }

    public function can_use_card_count($sign_card_list)
    {
        $count = 0;
        foreach ($sign_card_list as $sign_card) {
            if ($sign_card->status == SignCardRepository::NO_USE) {
                $count++;
            }
        }
        echo $count;
    }

    public function card_list($sign_card_list)
    {
        $html = '';
        $status = true;
        foreach($sign_card_list as $sign_card) {
            switch ($sign_card->card->name) {
                case '12月九折卡':
                    $card_name = '9折';
                    $card_description = '早起打卡报名9折代金券';
                    break;
                case '九折卡':
                    $card_name = '9折';
                    $card_description = '早起打卡报名9折代金券';
                    break;
                case '五折卡':
                    $card_name = '5折';
                    $card_description = '早起打卡报名5折代金券';
                    break;
                case '全免卡':
                    $card_name = '全免';
                    $card_description = '早起打卡报名全免代金券';
                    break;
                case '补签卡':
                    $card_name = '补签';
                    $card_description = '早起打卡漏签补签券';
                    break;
                default:
                    $status = false;
                    break;
            }
            if ($sign_card->status == SignCardRepository::NO_USE) {
                $disable = '';
                $disabled = '';
            } else {
                $disable = 'disable';
                $disabled = 'disabled';
            }
            $card_date = '有效期限：'. Carbon::parse($sign_card->start_at)->toDateString() . ' - '. Carbon::parse($sign_card->end_at)->toDateString();
            if ($status) {
                $html .= '<li class="'. $disable .'">
                            <div class="top">
                                <div class="card-name">'. $card_name .'</div>
                                <div class="card-button">
                                    <button type="button" class="use-btn" card-id="'. $sign_card->id .'" '. $disabled .'>点击使用</button>
                                </div>
                            </div>
                            <div class="bottom">
                                <div class="card-description">'. $card_description .'</div>
                                <div class="date">'. $card_date .'</div>
                            </div>
                        </li>';
            }
        }

        if (empty($html)) {
            $html = '<h4 style="text-align: center;color: #96d3d1;margin-top: 20px">您还没有卡券喔~</h4>';
        }

        echo $html;
    }
}