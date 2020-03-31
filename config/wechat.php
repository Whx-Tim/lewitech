<?php

return [
    /*
     * Debug 模式，bool 值：true/false
     *
     * 当值为 false 时，所有的日志都不会记录
     */
    'debug'  => true,

    /*
     * 使用 Laravel 的缓存系统
     */
    'use_laravel_cache' => true,

    /*
     * 账号基本信息，请从微信公众平台/开放平台获取
     */
    'app_id'  => env('WECHAT_APPID', 'wx7254b129dd96523e'),         // AppID
    'secret'  => env('WECHAT_SECRET', '18ed0066c0414b412a126f8dce137237'),     // AppSecret
    'token'   => env('WECHAT_TOKEN', 'PDexlkuvafmE5xEQ5DrjpqZl3nZ6'),          // Token
    'aes_key' => env('WECHAT_AES_KEY', 'p0Wx0bxRUPQ7mOuBZVJQtrtUwRfTXswOp20YoFfQCVp'),                    // EncodingAESKey

    /**
     * 开放平台第三方平台配置信息
     */
    //'open_platform' => [
        /**
         * 事件推送URL
         */
        //'serve_url' => env('WECHAT_OPEN_PLATFORM_SERVE_URL', 'serve'),
    //],
    
    /*
     * 日志配置
     *
     * level: 日志级别，可选为：
     *                 debug/info/notice/warning/error/critical/alert/emergency
     * file：日志文件位置(绝对路径!!!)，要求可写权限
     */
    'log' => [
        'level' => env('WECHAT_LOG_LEVEL', 'debug'),
        'file'  => env('WECHAT_LOG_FILE', storage_path('logs/wechat.log')),
    ],

    /*
     * OAuth 配置
     *
     * only_wechat_browser: 只在微信浏览器跳转
     * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
     * callback：OAuth授权完成后的回调页地址(如果使用中间件，则随便填写。。。)
     */
     'oauth' => [
//         'only_wechat_browser' => false,
         'scopes'   => array_map('trim', explode(',', env('WECHAT_OAUTH_SCOPES', 'snsapi_userinfo'))),
//         'callback' => env('WECHAT_OAUTH_CALLBACK', '/examples/oauth_callback.php'),
     ],

    /*
     * 微信支付
     */
     'payment' => [
         'merchant_id'        => env('WECHAT_PAYMENT_MERCHANT_ID', '1487263682'),
         'key'                => env('WECHAT_PAYMENT_KEY', '4ME0sT84TU0RCHfmGqgQpVvtS9CMr8Vd'),
         'cert_path'          => env('WECHAT_PAYMENT_CERT_PATH', '/home/wx.lewitech.cn/lewitech/cert/apiclient_cert.pem'), // XXX: 绝对路径！！！！
         'key_path'           => env('WECHAT_PAYMENT_KEY_PATH', '/home/wx.lewitech.cn/lewitech/cert/apiclient_key.pem'),      // XXX: 绝对路径！！！！
//         'notify_url'         => env('WECHAT_NOTIFY_URL', 'http://wx.lewitech.cn/wechat/pay/response') // 支付结果回调地址
         // 'device_info'     => env('WECHAT_PAYMENT_DEVICE_INFO', ''),
         // 'sub_app_id'      => env('WECHAT_PAYMENT_SUB_APP_ID', ''),
         // 'sub_merchant_id' => env('WECHAT_PAYMENT_SUB_MERCHANT_ID', ''),
         // ...
     ],

    /*
     * 开发模式下的免授权模拟授权用户资料
     *
     * 当 enable_mock 为 true 则会启用模拟微信授权，用于开发时使用，开发完成请删除或者改为 false 即可
     */
//     'enable_mock' => env('WECHAT_ENABLE_MOCK', false),
//     'mock_user' => [
//         "openid" =>"oSiVJ0s1VNlyopzRrJZL4oCHbVVQ",
//         // 以下字段为 scope 为 snsapi_userinfo 时需要
//         "nickname" => "Timx",
//         "sex" =>"1",
//         "province" =>"广东省",
//         "city" =>"深圳",
//         "country" =>"中国",
//         "headimgurl" => "http://wx.qlogo.cn/mmopen/LpyhKkibQot6qxdSCP8IR5u2xiaCwScUicLKoibgESeicFoMc6DeqYZsvGkz7fIWdibWcUZbPfBlBTyVhia5yKITzT272h7zLMh4sXj/0",
//     ],
];
