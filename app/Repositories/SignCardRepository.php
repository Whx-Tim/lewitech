<?php
namespace App\Repositories;

use App\Models\Card;
use App\Models\SignCard;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SignCardRepository
{
    const NO_USE = 1;
    const USED = 2;

    private $register_cards = ['五折卡', '全免卡', '补签卡', '九折卡', '12月九折卡'];
    private $start_month;
    private $end_month;

    /**
     * @var SignCard $sign_card
     */
    private $sign_card;

    /**
     * @var User $user;
     */
    private $user;

    public function __construct(SignCard $signCard)
    {
        $this->sign_card = $signCard;

        $this->start_month = Carbon::now()->startOfMonth();
        $this->end_month = Carbon::now()->endOfMonth();
    }

    private function setUser(User $user = null)
    {
        if (!is_null($user)) {
            $this->user = $user;
        } else {
            $this->user = Auth::user();
        }
    }

    public function giveUserCard(User $user = null)
    {
        $this->setUser($user);
        if ($this->user->sign_info) {
            return false;
        }

        $cards = Card::whereIn('name', ['五折卡', '全免卡', '补签卡'])->get();
        if ($this->user->getUpInfo) {
            if ($this->user->getUpInfo->day_total >= 1) {
                foreach ($cards as $card) {
                    $this->attainCard($card);
                    if ($card->name == '补签卡') {
                        $this->attainCard($card);
                        $this->attainCard($card);
                    }
                }
            } else {
                foreach ($cards as $card) {
                    if ($card->name == '全免卡') {
                        continue;
                    }
                    $this->attainCard($card);
                }
            }
        } else {
            foreach ($cards as $card) {
                if ($card->name == '全免卡') {
                    continue;
                }
                $this->attainCard($card);
            }
        }

        return true;
    }

    public function attainCard(Card $card, User $user = null)
    {
        $this->setUser($user);
        $start_at = Carbon::now()->toDateTimeString();
        $end_at = Carbon::now()->addYears(10)->toDateTimeString();
        switch ($card->regulation_type) {
            case CardRepository::DURATION_TYPE:
                $end_at = Carbon::now()->addDays($card->duration)->toDateTimeString();
                break;
            case CardRepository::CONFINE_TYPE:
                $start_at = $card->start_at;
                $end_at = $card->end_at;
                break;
            case CardRepository::FOREVER_TYPE:
            default:
                break;
        }

        return $this->user->sign_cards()->create([
            'card_id' => $card->id,
            'start_at' => $start_at,
            'end_at' => $end_at,
            'status' => self::NO_USE,
        ]);
    }

    public function useCardByName($name, User $user = null)
    {
        $this->setUser($user);

        $sign_card = $this->user->sign_cards()->where('status', SignCardRepository::NO_USE)->whereHas('card', function ($query) use ($name) {
            $query->where('name', $name);
        })->first();

        if ($sign_card) {
            $sign_card->status = self::USED;
            $sign_card->save();

            return $sign_card;
        } else {
            return false;
        }
    }

    public function haveCards(User $user = null)
    {
        $this->setUser($user);

        return $this->user->sign_cards()->where('status', SignCardRepository::NO_USE)->whereHas('card', function ($query) {
            $query->whereIn('name', ['五折卡', '全免卡', '九折卡', '12月九折卡']);
        })->with('card')->get();
    }

    public function canUseBySignCard(SignCard $signCard)
    {
        if (Carbon::now()->gte(Carbon::parse($signCard->start_at)) && Carbon::now()->lte(Carbon::parse($signCard->end_at))) {
            if ($signCard->status == self::NO_USE) {
                if (in_array($signCard->card->name, $this->register_cards)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isOwnerBySignCardId($id, User $user = null)
    {
        $this->setUser($user);

        return $this->user->sign_cards()->find($id);
    }

    public function getCards(User $user = null)
    {
        $this->setUser($user);

        return $this->user->sign_cards;
    }

}