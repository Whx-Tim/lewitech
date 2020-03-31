@extends('layouts.wechat')

@section('title', '校徽排行榜')

@section('css')
    <style>
        body {
            background-color: #ecebeb;
        }
        section.container {
            position: relative;
        }

        ._preview-container {
            display: block;
            width: 100vw;
            border-bottom: 10px solid #d3d4d6;
            position: relative;
        }
        ._preview-container > ._bg-img {
            width: 100%;
            display: block;
        }
        ._preview-container > ._user-avatar {
            position: absolute;
            top: 9vw;
            left: 0;
            width: 100vw;
            display: block;
            text-align: center;
        }
        ._rank-container {
            background-color: #ecebeb;
        }
        ._rank-container > h4 {
            text-align: center;
            margin-top: 5px;
        }
        ._rank-list {
            padding-bottom: 50px;
        }
        ._rank-list > li {
            margin: 8px 0;
            text-align: center;
            counter-increment: rank-counter;
        }
        ._rank-item {
            display: inline-block;
            width: 90%;
            border-radius: 10px;
            text-align: left;
            background-color: white;
            color: #525252;
            position: relative;
        }
        ._rank-item:after {
            position: absolute;
            content: counter(rank-counter);
            top: 10px;
            right: 10px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: white;
            font-size: 16px;
            text-align: center;
            line-height: 30px;
            font-weight: bolder;
            border: 1px solid #525252;
        }
        ._rank-list > li:nth-of-type(1) > ._rank-item:after {
            color: #e6b706;
            border: none;
        }
        ._rank-list > li:nth-of-type(2) > ._rank-item:after {
            color: #cb7227;
            border: none;
        }
        ._rank-list > li:nth-of-type(3) > ._rank-item:after {
            color: #cf5c4f;
            border: none;
        }

        ._rank-list > li:nth-of-type(1) > ._rank-item {
            background-color: #e6b706;
            color: white;
        }
        ._rank-list > li:nth-of-type(2) > ._rank-item {
            background-color: #cb7227;
            color: white;
        }
        ._rank-list > li:nth-of-type(3) > ._rank-item {
            background-color: #cf5c4f;
            color: white;
        }
        ._rank-item > img {
            margin: 5px 10px;
            vertical-align: middle;
            width: 40px;
        }
        ._rank-item > h5 {
            font-weight: bolder;
            font-size: 16px;
            display: inline-block;
        }
        ._footer-btn-area {
            position: fixed;
            width: 100vw;
            height: 10vh;
            bottom: 0;
            left: 0;
            display: block;
            text-align: center;
        }
        ._footer-btn-area > .generate-btn {
            width: 60%;
            background-color: #075ebb;
            font-size: 16px;
            border: none;
            border-radius: 20px;
            color: white;
            padding: 10px 0;
        }
        ._user-avatar > img {
            width: 42vw;
        }
        ._user-avatar > p {
            margin: 0;
            padding: 0;
            font-size: 15px;
            font-weight: bolder;
            text-align: center;
            color: white;
        }
        ._rank-container > h4 > img {
            width: 40px;
            vertical-align: middle;
        }
        ._touch-logo {
            position: absolute;
            top: 20px;
            left: 10px;
            width: 20vw;

        }
        ._counter {
            position: absolute;
            top: 10px;
            right: 10px;
            max-width: 30vw;
            text-align: right;
            font-size: 3vw;
        }
        ._counter > p:nth-of-type(1) {
            display: inline-block;
            background-color: #e13124;
            color: white;
            text-align: center;
            padding: 3px 5px;
            position: relative;
            border-radius: 5px;
        }
        ._counter > p:nth-of-type(1) > img {
            position: absolute;
            top: -5px;
            right: -3px;
            width: 5vw;
        }
        ._counter > p:nth-of-type(2) {
            display: block;
            color: white;
            text-align: right;
        }

    </style>
@endsection

@section('content')
    <section class="container">
        <div class="_preview-container">
            <div class="_counter">
                <p>
                    {{ $school_count }}
                    <img src="http://wj.qn.h-hy.com/images/lewitech/badge/counter-icon.png">
                </p>
                <p>支持人数</p>
            </div>
            <img src="http://wj.qn.h-hy.com/images/lewitech/badge/rank-top-bg.png" class="_bg-img">
            <div class="_user-avatar">
                <img src="{{ asset($user_avatar) }}" alt="">
                <p>请长按保存您的校徽头像~</p>
            </div>
        </div>
        <div class="_rank-container">
            <h4>
                <img src="{{ $school->badge_url }}" alt="">
                您的学校排名是第{{ $school_rank+1 }}名
            </h4>
            <ul class="_rank-list">
                @foreach($school_tops as $school_top)
                    <li>
                        <div class="_rank-item">
                            <img src="{{ $school_top->school->badge_url }}">
                            <h5>{{ $school_top->school->name }}</h5>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="_footer-btn-area">
            <button type="button" class="generate-btn">转发为母校代言</button>
        </div>
    </section>
@endsection

@section('javascript')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        $(document).ready(function () {
            $('.generate-btn').on('click', function () {
                $.ajax({
                    url: '{{ route('wechat.badge.check.share', ['school_id' => $school->id]) }}',
                    type: 'get',
                    data: {},
                    dataType: 'json',
                    success: function (data) {
                        if (data.code) {
                            toastr.error(data.message);
                        } else {
                            window.location.href = data.data.redirect;
                        }
                    },
                    error: function () {
                        toastr.error('当前生成人数过多，请稍后重试');
                    }
                })
            })

            function setShare(title)
            {
                var desc=title;
                var link='{{ route('wechat.badge.index') }}';
                var type='link';
                var imgUrl='{{ asset($user_avatar) }}';
                var dataUrl='';
                wx.onMenuShareTimeline({
                    title: title, // 分享标题
                    link: link, // 分享链接
                    imgUrl: imgUrl, // 分享图标
                    success: function () {
                        onshare("redHat","Timeline");
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
                wx.onMenuShareAppMessage({
                    title: title, // 分享标题
                    desc: desc, // 分享描述
                    link: link, // 分享链接
                    imgUrl: imgUrl, // 分享图标
                    type: type, // 分享类型,music、video或link，不填默认为link
                    dataUrl: dataUrl, // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () {
                        onshare("redHat","AppMessage");
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
                wx.onMenuShareQQ({
                    title: title, // 分享标题
                    desc: desc, // 分享描述
                    link: link, // 分享链接
                    imgUrl: imgUrl, // 分享图标
                    success: function () {
                        onshare("redHat","ShareQQ");
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
            }
            wx.config(<?php echo $js->config(array('onMenuShareQQ','onMenuShareAppMessage', 'onMenuShareTimeline'), false) ?>);
            wx.ready(function(){
                setShare('{{ Auth::user()->detail->nickname }}邀您一起助力生成{{ $school->name }}校徽头像，快来参加吧~');
            });
        });
    </script>
@endsection