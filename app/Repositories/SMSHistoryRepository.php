<?php
namespace App\Repositories;

use App\Models\SmsHistroy;

class SMSHistoryRepository
{
    const SEND_SUCCESS = 1;
    const SEND_FAIL = 2;

    private $sms_history;

    public function __construct()
    {
        $this->sms_history = new SmsHistroy();
    }

}