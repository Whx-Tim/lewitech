<?php

namespace App\Listeners;

use App\Events\SendPhoneCode;
use App\Events\TriggerWarning;
use App\Models\SmsHistroy;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

include_once app_path('vendor/Ucpaas.class.php');
use Ucpaas;

class SendCodeSMSToPhone
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SendPhoneCode  $event
     * @return void
     */
    public function handle(SendPhoneCode $event)
    {
        $phone = $event->phone;
        $code = $event->code;

        $host = "http://sms.market.alicloudapi.com";
        $path = "/singleSendSms";
        $method = "GET";
        $appcode = "2cba71d25f364f8f9fbc2dca012fc378";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $bodys = [
            'paramString' => '{"code":"'. $code .'"}',
            'RecNum' => $phone,
            'SignName' => '乐微科技',
            'TemplateCode' => 'SMS_90055007'
        ];
        $querys = "ParamString={$bodys['paramString']}&RecNum={$bodys['RecNum']}&SignName={$bodys['SignName']}&TemplateCode={$bodys['TemplateCode']}";

        $url = $host . $path . "?" . $querys;
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_FAILONERROR, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, true);
            if (1 == strpos("$".$host, "https://"))
            {
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            }
            $curl_info = curl_exec($curl);
            $curl_info = json_decode(substr($curl_info, strpos($curl_info, '{')));
            if ($curl_info->success) {
                $info = [
                    'phone' => $phone,
                    'status' => 1,
                    'ip' => request()->ip(),
                ];
            } else {
                $info = [
                    'phone' => $phone,
                    'status' => 2,
                    'ip' => request()->ip(),
                    'message' => $curl_info->message
                ];
            }
            SmsHistroy::create($info);
            curl_close($curl);


        } catch (\Exception $exception) {
            Log::warning($exception);
            event(new TriggerWarning('用户注册短信验证出现bug,请查看相关日志'));
        }

//        $options['accountsid'] = 'f5f2a92478ea6eb8f9f5749fa7713094';
//        $options['token'] = '1501e5f463ffc500fcb502ca892e9166';
//
//        $ucpass = new Ucpaas($options);
//
//        $appid = '112065c40c6548a6926395fe12527ddd';
//        $to = $event->phone;
//        $templateid = '45962';
//        $params = $event->code;
//
//        echo $ucpass->templateSMS($appid,$to,$templateid,$params);
    }

}
