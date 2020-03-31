<?php

    Route::get('badge', function () {
        return view('wechat.badge.index');
    });
    Route::get('badge/rank', function () {
        return view('wechat.badge.rank');
    });