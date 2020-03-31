<?php
namespace App\Repositories;

use App\Library\Traits\SelfClass;
use App\Models\Blacklist;

class BlacklistRepository
{
    protected $blacklist;

    public function __construct(Blacklist $blacklist)
    {
        $this->blacklist = $blacklist;
    }

    public function all()
    {
        return $this->blacklist->orderBy('created_at', 'desc')->get();
    }

    public function delete($id)
    {
        return $this->blacklist->where('user_id', $id)->delete();
    }

    public function test()
    {
        return 'test';
    }

}