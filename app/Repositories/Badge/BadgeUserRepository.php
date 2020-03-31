<?php
namespace App\Repositories\Badge;

use App\Library\Traits\SelfClass;
use App\Models\Badge\Badge;
use App\Models\Badge\BadgeUser;
use App\Models\User;

class BadgeUserRepository
{
    use SelfClass;

    private $model;

    public function __construct()
    {
        $this->model = new BadgeUser();
    }

    public function bindBadge(User $user, $badge_id)
    {
        $badge = $this->getBadgeUser($user);
        if ($badge) {
            $badge->badge_id = $badge_id;
            $badge->save();
        } else {
            $badge = $this->model->create([
                'user_id' => $user->id,
                'badge_id' => $badge_id,
            ]);
        }

        return $badge;
    }

    public function getBadgeUser(User $user)
    {
        $badges_id = WorldRepository::self()->getAllBadgeId();

        return $this->model->where('user_id', $user->id)->whereIn('badge_id', $badges_id)->with('badge')->first();
    }
}