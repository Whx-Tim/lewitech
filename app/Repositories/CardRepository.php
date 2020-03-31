<?php
namespace App\Repositories;

use App\Models\Card;
use Illuminate\Http\Request;

class CardRepository
{
    const DURATION_TYPE = 1;
    const CONFINE_TYPE = 2;
    const FOREVER_TYPE = 3;

    const CAN_USE = 1;

    private $card;

    public function __construct(Card $card)
    {
        $this->card = $card;
    }

    /**
     * 签到打卡使用的卡券初始化
     */
    public function signInit()
    {
        $this->card->create([
            'name' => '五折卡',
            'description' => '早起打卡可以进行五折报名',
            'status' => self::CAN_USE,
            'regulation' => 0.5,
            'regulation_type' => self::DURATION_TYPE,
            'duration' => '180',
        ]);
        $this->card->create([
            'name' => '全免卡',
            'description' => '早起打卡选择收费报名时可以免除押金报名，并参与最后的奖金瓜分',
            'status' => self::CAN_USE,
            'regulation' => 0,
            'regulation_type' => self::DURATION_TYPE,
            'duration' => '180',
        ]);
        $this->card->create([
            'name' => '补签卡',
            'description' => '早起打卡补签',
            'status' => self::CAN_USE,
            'regulation' => 0,
            'regulation_type' => self::DURATION_TYPE,
            'duration' => '180',
        ]);
    }

    public function create(Request $request)
    {
        return $this->card->create($this->filterEmptyData($request->only($this->card->getFillable())));
    }

    public function update(Request $request, $id)
    {
        return $this->card->where('id', $id)->update($this->filterEmptyData($request->only($this->card->getFillable())));
    }

    public function delete($id)
    {
        return $this->card->where('id', $id)->delete();
    }

    public function getAllCard()
    {
        return $this->card->orderBy('created_at', 'desc')->get();
    }

    private function filterEmptyData($data)
    {
        foreach ($data as $key => $datum) {
            if (empty($datum)) {
                unset($data[$key]);
            }
        }

        return $data;
    }



}