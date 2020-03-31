<?php

namespace App\Listeners;

use App\Events\SendSMS;
use App\Events\TriggerWarning;
use App\Models\SmsHistroy;
use App\Models\UmbrellaHistory;
use App\Repositories\SMSHistoryRepository;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendSMSToPhone
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
     * @param  SendSMS  $event
     * @return void
     */
    public function handle(SendSMS $event)
    {
        $phone = $event->phone;
        $data = $event->data;
        $type = $event->type;

        $accessKeyId = "LTAI2CSE2xlAxDBG";
        $accessKeySecret = "Y5NZTPzFPCf62rWJCs8lmwTKHPcMMX";

        $params = call_user_func_array([new self(), $type], [$phone, $data]);

        try {
            $content = $this->request(
                $accessKeyId,
                $accessKeySecret,
                "dysmsapi.aliyuncs.com",
                array_merge($params, array(
                    "RegionId" => "cn-hangzhou",
                    "Action" => "SendSms",
                    "Version" => "2017-05-25",
                ))
            );
            $info = [
                'phone' => $phone,
                'code' => $content->Code,
                'message' => $content->Message,
                'request_id' => $content->RequestId,
                'biz_id' => $content->BizId,
                'ip' => request()->ip(),
            ];
//            Log::warning($content);
        } catch (\Exception $exception) {
            $info = [
                'phone' => $phone,
                'code' => 'FAIL',
                'ip' => request()->ip(),
            ];
            Log::warning($exception);
            event(new TriggerWarning('短信发送异常,请查看相关日志'));
        }
        SmsHistroy::create($info);


//        $host = "http://sms.market.alicloudapi.com";
//        $path = "/singleSendSms";
//        $method = "GET";
//        $appcode = "2cba71d25f364f8f9fbc2dca012fc378";
//        $headers = array();
//        array_push($headers, "Authorization:APPCODE " . $appcode);
//
//
//        $bodys = call_user_func_array([new self(), $type], [$phone, $date]);
//        $querys = "ParamString={$bodys['paramString']}&RecNum={$bodys['RecNum']}&SignName={$bodys['SignName']}&TemplateCode={$bodys['TemplateCode']}";
//
//        $url = $host . $path . "?" . $querys;
//        try {
//            $curl = curl_init();
//            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
//            curl_setopt($curl, CURLOPT_URL, $url);
//            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//            curl_setopt($curl, CURLOPT_FAILONERROR, false);
//            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($curl, CURLOPT_HEADER, true);
//            if (1 == strpos("$".$host, "https://"))
//            {
//                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
//            }
//            $curl_info = curl_exec($curl);
//            $curl_info = json_decode(substr($curl_info, strpos($curl_info, '{')));
//            if ($curl_info->success) {
//                $info = [
//                    'phone' => $phone,
//                    'status' => 1,
//                    'ip' => request()->ip(),
//                ];
//            } else {
//                $info = [
//                    'phone' => $phone,
//                    'status' => 2,
//                    'ip' => request()->ip(),
//                    'message' => $curl_info->message
//                ];
//            }
//            SmsHistroy::create($info);
//            curl_close($curl);
//
//
//        } catch (\Exception $exception) {
//            Log::warning($exception);
//            event(new TriggerWarning('发送用户提醒归还短信出现bug,请查看相关日志'));
//        }
    }

    protected function umbrellaRegister($phone, $code)
    {
        return [
            'PhoneNumbers' => $phone,
            'SignName' => '乐微科技',
            'TemplateCode' => 'SMS_90055007',
            'TemplateParam' => json_encode([
                'code' => $code
            ], JSON_UNESCAPED_UNICODE)
        ];
    }

    protected function still($phone, $date)
    {
        return [
            'PhoneNumbers' => $phone,
            'SignName' => '乐微科技',
            'TemplateCode' => 'SMS_95455006',
            'TemplateParam' => json_encode([
                'date' => $date
            ], JSON_UNESCAPED_UNICODE)
        ];
    }

    protected function stillRemind($phone, $date)
    {
        return [
            'PhoneNumbers' => $phone,
            'SignName' => '乐微科技',
            'TemplateCode' => 'SMS_94810044',
            'TemplateParam' => json_encode([
                'date' => $date
            ], JSON_UNESCAPED_UNICODE)
        ];
    }

    /**
     * 生成签名并发起请求
     *
     * @param $accessKeyId string AccessKeyId (https://ak-console.aliyun.com/)
     * @param $accessKeySecret string AccessKeySecret
     * @param $domain string API接口所在域名
     * @param $params array API具体参数
     * @param $security boolean 使用https
     * @return bool|\stdClass 返回API接口调用结果，当发生错误时返回false
     */
    public function request($accessKeyId, $accessKeySecret, $domain, $params, $security=false) {
        $apiParams = array_merge(array (
            "SignatureMethod" => "HMAC-SHA1",
            "SignatureNonce" => uniqid(mt_rand(0,0xffff), true),
            "SignatureVersion" => "1.0",
            "AccessKeyId" => $accessKeyId,
            "Timestamp" => gmdate("Y-m-d\TH:i:s\Z"),
            "Format" => "JSON",
        ), $params);
        ksort($apiParams);

        $sortedQueryStringTmp = "";
        foreach ($apiParams as $key => $value) {
            $sortedQueryStringTmp .= "&" . $this->encode($key) . "=" . $this->encode($value);
        }

        $stringToSign = "GET&%2F&" . $this->encode(substr($sortedQueryStringTmp, 1));

        $sign = base64_encode(hash_hmac("sha1", $stringToSign, $accessKeySecret . "&",true));

        $signature = $this->encode($sign);

        $url = ($security ? 'https' : 'http')."://{$domain}/?Signature={$signature}{$sortedQueryStringTmp}";

        try {
            $content = $this->fetchContent($url);
            return json_decode($content);
        } catch( \Exception $e) {
            return false;
        }
    }

    private function encode($str)
    {
        $res = urlencode($str);
        $res = preg_replace("/\+/", "%20", $res);
        $res = preg_replace("/\*/", "%2A", $res);
        $res = preg_replace("/%7E/", "~", $res);
        return $res;
    }

    private function fetchContent($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "x-sdk-client" => "php/2.0.0"
        ));

        if(substr($url, 0,5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $rtn = curl_exec($ch);

        if($rtn === false) {
            trigger_error("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), E_USER_ERROR);
        }
        curl_close($ch);

        return $rtn;
    }
}
