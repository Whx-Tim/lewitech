<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('home', 'HomeController@index')->middleware(['web', 'wechat.oauth']);
Route::any('/wechat/server', 'Wechat\ServerController@server');

//TEMP ROUTE
    Route::get('video', 'Wechat\TempActiveController@video');

Route::group([
    'middleware' => ['web'],
    'prefix' => 'wechat/pay',
    'as' => 'wechat.pay.response.',
    'namespace' => 'Wechat'
], function () {
    Route::post('response', 'ServerController@payResponse')->name('index');
    Route::get('umbrella', 'UmbrellaController@showGratuity')->name('umbrella.gratuity')->middleware(['wechat.oauth']);
    Route::post('umbrella', 'UmbrellaController@gratuity');
    Route::get('umbrella/check', 'UmbrellaController@gratuityOrderResponse')->name('umbrella.check');
//    Route::post('response/sign', '')
});

//Auth::routes();

Route::group([
    'middleware' => [],
    'prefix' => 'redirect',
    'as' => 'redirect.'
], function () {
    Route::get('{redirect}', 'RedirectController@index');
});

/**
 * 后台管理路由
 */
Route::group([
    'middleware' => 'web',
    'namespace'  => 'Admin',
    'as'         => 'admin.',
    'prefix'     => 'admin'
], function () {
    Route::get('/login', 'IndexController@showLogin')->name('login');
    Route::get('/logout', 'IndexController@logout')->name('logout');
    Route::post('/login', 'IndexController@adminLogin');

    Route::group([
        'middleware' => ['admin'],
    ], function () {
        Route::get('/', 'IndexController@index')->name('index');
        Route::get('getQiniuToken', 'IndexController@getQiniuToken')->name('getUploadToken');

        /**
         * 用户管理路由
         */
        Route::group([
            'middleware' => [],
            'as'         => 'users.',
            'prefix'     => 'user'
        ], function () {
            Route::get('/', 'UserController@index')->name('index');
            Route::get('detail/{user}', 'UserController@showDetail')->name('detail');
        });

        /**
         * 活动管理路由
         */
        Route::group([
            'middleware' => [],
            'as'         => 'active.',
            'prefix'     => 'active'
        ], function () {
            Route::get('/', 'ActiveController@index')->name('index');
            Route::get('edit/{cache_active}', 'ActiveController@edit')->name('edit');
            Route::get('detail/{cache_active}', 'ActiveController@detail')->name('detail');
            Route::get('delete/{cache_active}', 'ActiveController@delete')->name('delete');
            Route::get('add', 'ActiveController@add')->name('add');
            Route::get('check/{cache_active}', 'ActiveController@check')->name('check');
            Route::post('add', 'ActiveController@store');
            Route::post('edit/{cache_active}', 'ActiveController@update');

            /**
             * banner管理路由
             */
            Route::group([
                'middleware' => [],
                'as'         => 'banner.',
                'prefix'     => 'banner'
            ], function () {
                Route::get('/', 'ActiveController@bannerIndex')->name('index');
                Route::get('add', 'ActiveController@addBanner')->name('add');
                Route::get('edit/{id}', 'ActiveController@editBanner')->name('edit');
                Route::post('add', 'ActiveController@storeBanner');
                Route::post('edit/{id}', 'ActiveController@updateBanner');
            });

            Route::group([
                'middleware' => [],
                'as'         => 'enroll.',
                'prefix'     => 'enroll'
            ], function () {
                Route::get('enroll/{active}', 'ActiveController@showEnroll')->name('index');
            });
        });

        /**
         * 需求管理路由
         */
        Route::group([
            'middleware' => [],
            'as'         => 'demand.',
            'prefix'     => 'demand'
        ], function () {
            Route::get('/', 'DemandController@index')->name('index');
            Route::get('edit/{cache_demand}', 'DemandController@edit')->name('edit');
            Route::get('detail/{cache_demand}', 'DemandController@detail')->name('detail');
            Route::get('delete/{cache_demand}', 'DemandController@delete')->name('delete');
            Route::get('check/{cache_demand}', 'DemandController@check')->name('check');
            Route::get('add', 'DemandController@add')->name('add');
            Route::get('{cache_demand}/enrolls', 'DemandController@enrollsUsers')->name('enrolls');
            Route::get('{cache_demand}/view', 'DemandController@viewUsers')->name('views');
            Route::post('add', 'DemandController@store');
            Route::post('edit/{cache_demand}', 'DemandController@update');
        });

        /**
         * 公益伞路由
         */
        Route::group([
            'middleware' => [],
            'as'         => 'umbrella.',
            'prefix'     => 'umbrella'
        ], function () {
            Route::get('/', 'UmbrellaController@index')->name('index');
        });

        /**
         * 校企地图路由
         */
        Route::group([
            'middleware' => [],
            'as'         => 'business.',
            'prefix'     => 'business'
        ], function () {
            Route::get('/', 'BusinessController@index')->name('index');
        });

        Route::group([
            'middleware' => [],
            'as' => 'blacklist.',
            'prefix' => 'blacklist'
        ], function () {
//            Route::get('/', '')
        });
    });


});

/**
 * 微信路由
 */
Route::group([
    'middleware' => ['web'],
    'namespace'  => 'Wechat',
    'prefix'     => 'wechat',
    'as'         => 'wechat.'
], function () {
    /**
     * 临时使用路由
     */
    Route::group([
        'middleware' => ['web'],
        'prefix'     => 'temp/',
        'as'         => 'temp.'
    ], function () {
        Route::get('{name}', 'TempActiveController@show')->name('show');
        Route::post('{type}/apply', 'TempActiveController@apply')->name('apply');
    });

    Route::group([
        'middleware' => ['web'],
        'prefix'     => 'oauth',
        'as'         => 'oauth.'
    ], function () {
        Route::get('code', 'OAuthController@getCode')->name('code')->middleware(['wechat.basic']);
        Route::post('getuserinfo', 'OAuthController@getUserInfo')->name('user.info');
    });

    Route::group([
        'middleware' => [],
        'prefix'     => 'badge',
        'as'         => 'badge.'
    ], function () {
        Route::group([
            'middleware' => ['subscribe:badge', 'wechat.oauth:badge']
        ], function () {
            Route::get('/', 'BadgeController@index')->name('index');
            Route::get('rank', 'BadgeController@rank')->name('rank');
            Route::get('share', 'BadgeController@share')->name('share');
            Route::get('search', 'BadgeController@search')->name('search');
            Route::get('check/share', 'BadgeController@checkShareImage')->name('check.share');
            Route::post('combine', 'BadgeController@combine')->name('combine');
        });

        Route::get('subscribe', 'BadgeController@subscribe')->name('subscribe');

        Route::group([
            'middleware' => ['wechat.basic', 'wechat.login'],
            'prefix' => 'world',
            'as' => 'world.',
            'namespace' => 'Badge'
        ], function () {
            Route::get('/', 'WorldController@index')->name('index')->middleware(['subscribe:badge']);
            Route::get('avatar', 'WorldController@avatar')->name('avatar');
            Route::get('share', 'WorldController@share')->name('share');
            Route::post('save/share', 'WorldController@saveShare')->name('check.share');
            Route::post('combine', 'WorldController@combine')->name('combine')->middleware(['subscribe:badge']);

            Route::get('subscribe', 'WorldController@subscribe')->name('subscribe');
        });
    });

    Route::group([
        'middleware' => ['web'],
        'prefix' => 'help',
        'as' => 'help.'
    ], function () {
        Route::post('response', 'HelpController@payResponse');
        Route::get('pay/response', 'HelpController@payResponse')->name('check.response');
        Route::get('pay/check', 'HelpController@checkOrder')->name('pay.check');

        Route::group([
            'middleware' => ['wechat.oauth']
        ], function () {
            Route::get('/', 'HelpController@index')->name('index');
            Route::group([
                'middleware' => ['help.apply']
            ], function () {
                Route::get('apply', 'HelpController@showApply')->name('apply');


                Route::post('apply', 'HelpController@apply');
            });

        });
    });

    Route::group([
        'middleware' => ['wechat.oauth'],
        'prefix'     => 'pay',
        'as'         => 'pay.'
    ], function () {
        Route::get('everyDay', 'WechatDealController@everyDay')->name('everyDay');
        Route::post('everyDay', 'WechatDealController@order');
        Route::get('check', 'WechatDealController@orderResponse')->name('response');
        Route::get('refund/{wechat_deal}', 'WechatDealController@refund')->name('refund');
        Route::get('safe', 'WechatDealController@showSafe')->name('safe');
    });

    Route::group([
        'middleware' => ['web'],
        'prefix' => 'daysign',
        'as' => 'daysign.'
    ], function () {
        Route::group([
            'middleware' => ['wechat.basic', 'wechat.login']
        ], function () {
            Route::get('/', 'DaySignController@index')->name('index');
            Route::get('setting', 'DaySignController@setting')->name('setting');
            Route::get('order/check', 'DaySignController@checkOrderPay')->name('order.check');
            Route::get('sign', 'DaySignController@sign')->name('sign');
            Route::post('order', 'DaySignController@order')->name('order');
        });
        Route::post('response', 'DaySignController@response')->name('response');
    });

    Route::group([
        'middleware' => ['web'],
        'prefix'     => 'sign',
        'as'         => 'sign.'
    ], function () {
        Route::post('response', 'SignController@payResponse')->name('response.pay');
        Route::post('response/donate', 'SignController@donateResponse')->name('response.donate');
        Route::get('test', 'SignController@test');
        Route::get('reward/total', 'SignController@showTotalReward');

        Route::group([
            'middleware' => ['wechat.oauth', 'subscribe']
        ], function () {
            Route::group([
                'middleware' => ['sign_apply']
            ], function () {
                Route::get('/', 'SignController@index')->name('index');
                Route::get('info', 'SignController@showInfo')->name('info');
                Route::get('setting', 'SignController@showSetting')->name('setting');
                Route::get('donate', 'SignController@showDonate')->name('donate');
                Route::get('withdraw', 'SignController@showWithdraw')->name('withdraw');
                Route::get('donate/info', 'SignController@showDonateInfo')->name('donate.info');
                Route::get('donate/cert/{user}', 'SignController@showDonateCert')->name('donate.cert');
                Route::get('rank/reward', 'SignController@showRewardRank')->name('rank.reward');
            });
            Route::get('donate/check', 'SignController@donateOrderResponse')->name('donate.check');
            Route::get('free2deposit', 'SignController@showFree2depositApply')->name('apply.free2deposit');
            Route::get('apply', 'SignController@showApply')->name('apply');
            Route::get('apply/confirm', 'SignController@showApplyConfirm')->name('apply.confirm');
            Route::get('apply/share', 'SignController@showApplyShare')->name('apply.share');
            Route::get('check/pay', 'SignController@orderResponse')->name('check.pay');
            Route::get('check/free2deposit', 'SignController@free2depositCheck')->name('check.free2deposit');
            Route::get('clock', 'SignController@clock')->name('clock');
            Route::get('poster/week', 'SignController@showPosterWeek')->name('poster.week');
            Route::get('share/{user}', 'SignController@showShare')->name('share');
            Route::get('poster/share', 'SignController@showSharePoster')->name('poster.share');
            Route::get('week', 'SignController@showWeek')->name('week');
            Route::get('rank', 'SignController@showRank')->name('rank');

            Route::get('reward', 'SignController@showReward')->name('reward');
            Route::post('reward', 'SignController@getReward');

            Route::get('refuse', 'SignController@showRefuse')->name('refuse');
            Route::get('refuse/real', 'SignController@realRefuse')->name('refuse.real');
            Route::post('donate/info', 'SignController@perfectDonateInfo');
            Route::post('donate', 'SignController@donateToUmbrella')->name('donate.order');
            Route::post('apply', 'SignController@apply');
            Route::post('free2deposit', 'SignController@free2depositApply');
            Route::post('card', 'SignController@useCard')->name('card');
        });
    });

    /**
     * 股东需求发布路由
     */
   Route::group([
       'middleware' => ['wechat.oauth'],
       'prefix'     => 'demand'
   ], function () {
       Route::get('publish', 'DemandController@showPublish');
       Route::get('detail/{demand_view}', 'DemandController@detail');
       Route::get('check/{cache_demand}', 'DemandController@checkDemand');
       Route::get('help/{cache_demand}', 'DemandController@showHelp');
       Route::get('refuse/{cache_demand}', 'DemandController@refuse');
       Route::post('help/{cache_demand}', 'DemandController@help');
       Route::post('publish', 'DemandController@publish');
   });

    /**
     * 活动路由
     */
   Route::group([
       'middleware' => ['wechat.oauth'],
       'prefix'     => 'active'
   ], function () {
       Route::get('list', 'ActiveController@showList');
       Route::get('detail/{active_view}', 'ActiveController@showDetail');
       Route::get('publish', 'ActiveController@showPublish');
       Route::get('apply/{cache_active}', 'ActiveController@showApply');
       Route::get('getList', 'ActiveController@getActiveList');
       Route::get('search', 'ActiveController@search');
       Route::get('cancel/apply/{cache_active}', 'ActiveController@cancelApply');
       Route::post('publish', 'ActiveController@publish');
       Route::post('apply/{active}', 'ActiveController@apply');
   });

    /**
     * 共享雨伞路由
     */
    Route::group([
        'middleware' => ['wechat.oauth'],
        'prefix'     => 'umbrella',
        'as'         => 'umbrella.'
    ], function () {
        Route::get('register', 'UmbrellaController@showRegister')->name('register');
        Route::get('vcode', 'UmbrellaController@vcode')->name('vcode');
        Route::post('register', 'UmbrellaController@register');

        Route::group([
            'middleware' => ['umbrella']
        ], function () {
            Route::get('index', 'UmbrellaController@index')->name('index');
            Route::get('bind/{umbrella}', 'UmbrellaController@bindStationUmbrella')->name('bind');
            Route::get('pass', 'UmbrellaController@showPass')->name('pass');
            Route::get('history', 'UmbrellaController@showHistory')->name('history');
            Route::get('history/detail', 'UmbrellaController@showHistoryDetail')->name('history.detail');
            Route::get('forceStill', 'UmbrellaController@forceStill')->name('force-still');
            Route::get('prompt', 'UmbrellaController@showPrompt')->name('prompt');
            Route::get('cancelShare', 'UmbrellaController@cancelShare')->name('no-share');
            Route::get('openShare', 'UmbrellaController@openShare')->name('share');
            Route::get('gratuity', 'UmbrellaController@showGratuity')->name('.gratuity');
            Route::get('share/check', 'UmbrellaController@checkShare')->name('check.share');
            Route::get('share/result', 'UmbrellaController@showShareSuccess')->name('share.success');
        });
    });

    /*
     * 中秋画板路由
     */
    Route::group([
        'middleware' => ['web', 'wechat.oauth'],
        'prefix' => 'palette',
        'as' => 'palette.'
    ], function () {
        Route::get('/', 'PaletteController@index')->name('index');
        Route::get('/result/{palette}', 'PaletteController@result')->name('result');
        Route::get('/token/{type}', 'PaletteController@getToken')->name('token');
        Route::post('/save', 'PaletteController@save')->name('save');
    });

    /**
     * 校企地图路由
     */
    Route::group([
        'middleware' => ['web', 'wechat.oauth'],
        'as' => 'business.',
        'prefix' => 'business'
    ], function () {
        Route::get('getData', 'BusinessController@getData');
        Route::get('/', 'BusinessController@index')->name('index');
        Route::get('getBusiness', 'BusinessController@getIndex')->name('get.index');
        Route::get('add' , 'BusinessController@add')->name('add');
        Route::get('edit/{business}', 'BusinessController@edit')->name('edit');
        Route::get('detail/{business}', 'BusinessController@detail')->name('detail');
        Route::get('discount/{business}', 'BusinessController@discount_detail')->name('discount');
        Route::get('comment/{business}', 'BusinessController@comment')->name('comment');
        Route::post('add', 'BusinessController@store');
        Route::post('edit/{business}', 'BusinessController@update');
        Route::post('comment/{business}', 'BusinessController@storeComment');
    });

    /**
     * 微信授权信息路由
     */
   Route::group([
       'middleware' => ['wechat.oauth']
   ], function () {
       Route::get('oauth/authorized', 'ServerController@redirectToCallback');
       Route::get('oauth/test', 'ServerController@callbackTest');
   });


   Route::group([
       'middleware' => ['wechat.oauth'],
       'prefix'     => 'report',
       'as'         => 'report.'
   ], function () {
       Route::get('appTest', 'ReportController@appTest')->name('app_test');
       Route::get('myReport', 'ReportController@myReport')->name('my');
       Route::post('appTest', 'ReportController@storeAppTestSuggest');
   });

   Route::get('red_hat', 'TempActiveController@red_hat')->name('red_hat')->middleware(['wechat.oauth']);

   Route::get('insurance', 'TempActiveController@insurance')->middleware('wechat.oauth');
   Route::post('insurance', 'TempActiveController@addInsurance')->middleware('wechat.oauth');
   Route::any('oauth/userinfo', 'ServerController@acceptAuthCode');
   Route::get('userInsert', 'ServerController@getUsersAndInsert');
   Route::get('subscribe', 'ServerController@showSubscribe')->name('subscribe');
});

/**
 * 微信签到打卡路由 旧版已弃用
 */
//Route::group([
//    'middleware' => ['web','wechat.oauth'],
//    'namespace'  => 'GetUp',
//    'prefix'     => 'wechat/getup',
//], function () {
//    Route::get('index', 'LeaderboardController@index');
//    Route::get('userinfo', 'LeaderboardController@getUserInfo');
//    Route::get('rankList', 'LeaderboardController@getGetUpRankList');
//    Route::get('daySumRankList', 'LeaderboardController@getDaySumRankList');
//    Route::get('award', 'LeaderboardController@showAward');
//    Route::get('dayTotalList', 'LeaderboardController@getDayTotalList');
//});


/**
 * 数据统计路由
 */
Route::group([
    'middleware' => ['web'],
    'prefix'     => 'data'
], function () {
    Route::get('getup', 'GetUp\LeaderboardController@data');
    Route::get('active617', 'Wechat\TempActiveController@showDataActive617');
    Route::get('umbrella', 'Admin\DataController@umbrella');
    Route::get('sign/reward', 'Wechat\SignController@showCarveUpReward');
    Route::get('insurance', 'Admin\DataController@insurance');
    Route::get('umbrella/stations', 'Admin\DataController@umbrella_station');
    Route::get('umbrella/operation', 'Admin\DataController@umbrella_operation');
});

Route::group([
    'middleware' => ['web'],
], function () {
    Route::get('weijuan_act_redirect', 'TempController@showWeijuanActRedirect');
    Route::get('localQRcode', 'TempController@localQRcode');
    Route::get('sms', 'TempController@testSMS');
});


Route::group([
    'middleware' => ['web', 'wechat.oauth'],
    'prefix' => 'api'
], function () {
    Route::get('getJSSDK', 'Wechat\ServerController@getJSSDK');
});

Route::group([
    'middleware' => ['web'],
    'as'         => 'web.',
    'namespace'  => 'Web'
], function () {
    Route::get('/', 'IndexController@index')->name('index');
    Route::get('introduction', 'IndexController@introduction')->name('introduction');
    Route::get('app', 'IndexController@app')->name('app');
    Route::get('sign', 'IndexController@sign')->name('sign');
    Route::get('umbrella', 'IndexController@umbrella')->name('umbrella');
    Route::get('test', 'IndexController@test');
});


