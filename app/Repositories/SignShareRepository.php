<?php
namespace App\Repositories;

use App\Models\SignShare;
use App\Models\SignTimer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SignShareRepository
{
    const SECOND_HELP = 2;
    const FIRST_HELP = 1;

    private $sign_share;

    /**
     * @var User
     */
    private $user;

    public function __construct(SignShare $signShare)
    {
        $this->sign_share = $signShare;
    }

    private function setUser(User $user = null)
    {
        if (is_null($user)) {
            $user = Auth::user();
        } else {
            $this->user = $user;
        }
    }


    public function create($user_id, $timer_id)
    {
        $sign_share = $this->sign_share->where([
            ['user_id', $user_id],
            ['help_id', Auth::id()],
            ['sign_timer_id', $timer_id]
        ])->first();
        if ($sign_share) {
            $sign_share->touch();
        } else {
            $sign_share = $this->sign_share->create([
                'user_id'       => $user_id,
                'help_id'       => Auth::id(),
                'sign_timer_id' => $timer_id
            ]);
        }

        return $sign_share;
    }

    public function firstHelpEnough(User $user, SignTimer $signTimer)
    {
        if ($user->sign_help_froms()->where('type', self::FIRST_HELP)->where('sign_timer_id', $signTimer->id)->count() < 5) {
            return true;
        }

        return false;
    }

    public function firstCanRecover(User $user, SignTimer $signTimer)
    {
        if ($user->sign_help_froms()->where('type', self::FIRST_HELP)->where('sign_timer_id', $signTimer->id)->count() == 4) {
            return true;
        }

        return false;
    }

    public function secondHelpEnough(User $user, SignTimer $signTimer)
    {
        if ($user->sign_help_froms()->where('type', self::SECOND_HELP)->where('sign_timer_id', $signTimer->id)->count() < 10) {
            return true;
        }

        return false;
    }

    public function secondCanRecover(User $user, SignTimer $signTimer)
    {
        if ($user->sign_help_froms()->where('type', self::SECOND_HELP)->where('sign_timer_id', $signTimer->id)->count() == 9) {
            return true;
        }

        return false;
    }

    public function help($id, User $user, SignTimer $signTimer)
    {
        return $user->sign_help_tos()->create([
            'user_id' => $id,
            'sign_timer_id' => $signTimer->id
        ]);
    }

    public function getHelpUserList(User $user = null)
    {
        $this->setUser($user);
        $user = $this->user;
        $sign_timer = new SignTimerRepository(new SignTimer());
        $timer = $sign_timer->getOpeningTimer();
        $sign_shares = null;
        if ($this->user->sign_info->status == SignInfoRepository::FIRST_LOST_SIGN || $this->user->sign_info->status == SignInfoRepository::RECOVER_FIRST_LOST_SIGN) {
            $sign_shares = $this->sign_share->where('sign_timer_id', $timer->id)->where('type', self::FIRST_HELP)->where('user_id', $user->id)->with('user_to.detail')->get();
//            $users =                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         User::whereHas('sign_help_froms', function ($query) use ($timer, $user) {
//                $query->where('sign_timer_id', $timer->id)->where('type', self::FIRST_HELP);
//            })->with('detail')->get();
        } else if ($this->user->sign_info->status == SignInfoRepository::SECOND_LOST_SIGN || $this->user->sign_info->status == SignInfoRepository::RECOVER_SECOND_LOST_SIGN) {
            $sign_shares = $this->sign_share->where('sign_timer_id', $timer->id)->where('type', self::SECOND_HELP)->where('user_id', $user->id)->with('user_to.detail')->get();
//            $users = User::whereHas('sign_help_froms', function ($query) use ($timer, $user) {
//                $query->where('sign_timer_id', $timer->id)->where('type', self::SECOND_HELP);
//            })->with('detail')->get();
        }

        return $sign_shares;
    }
}
