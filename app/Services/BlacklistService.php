<?php
namespace App\Services;

use App\Http\Requests\Blacklist\Request;
use App\Repositories\BlacklistRepository;

class BlacklistService
{

    protected $blacklist;

    public function __construct(BlacklistRepository $blacklistRepository)
    {
        $this->blacklist = $blacklistRepository;
    }

    public function getAll()
    {
        return $this->blacklist->all();
    }

    public function canAdd()
    {

    }

}