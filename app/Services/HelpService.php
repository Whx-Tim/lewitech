<?php
namespace App\Services;

use App\Models\User;
use App\Models\WechatDeal;
use App\Repositories\WechatDealRepository;
use Illuminate\Support\Facades\Auth;

class HelpService
{
    const STATUS_PAID = 1;

    private $type_map_array = [
        '重病互助' => 0
    ];

    public function is_apply(User $user)
    {
        $help = $this->getUserHelp($user);
        if ($help) {
            if ($help->status == self::STATUS_PAID) {
                return true;
            }
        }

        return false;
    }

    public function getUserHelp(User $user)
    {
        return $user->help;
    }

    public function apply($data, User $user)
    {
        check_arg($data, 'name', 'id_number');
        if (empty($data['type'])) {
            $data['type'] = $this->type_map_array['重病互助'];
        }
        if (empty($data['status'])) {
            $data['status'] = 0;
        }
        if (empty($data['deal_id'])) {
            $data['deal_id'] = null;
        }
        $help = $this->getUserHelp($user);
        if ($help) {
            $help->update($data);
        } else {
            $help = $user->help()->create($data);
        }

        return $help;
    }

    public function getHelpConfig()
    {
        $deal_repository = new WechatDealRepository(new WechatDeal());

        return $deal_repository->wechatPayOrder('乐微互助参与金', 10, url('wechat/help/response'));
    }

    public function response()
    {
        $deal_repository = new WechatDealRepository(new WechatDeal());
        return $deal_repository->baseResponse(function ($order) {
            $user = $order->user;
            $user->help->update([
                'deal_id' => $order->id,
                'status' => self::STATUS_PAID
            ]);
        });
    }
}