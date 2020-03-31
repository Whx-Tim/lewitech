@extends('layouts.wechat')

@section('title', '中秋节')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/palette/page.css') }}">
    <link href="https://cdn.bootcss.com/animate.css/3.5.2/animate.css" rel="stylesheet">
    <style>
        .btn-palette {
            display: inline-block;
            border-radius: 100%;
            width: 40px;
            height: 40px;
            font-size: 11px;
            padding: 5px;
            background-color: #3dd3c3;
            color: #ffffff;
        }

        .logo {
            position: fixed;
            width: 100%;
            text-align: center;
            margin-right: auto;
            margin-left: auto;
            bottom: 5px;
            left: 0;
            z-index: 110;
        }

        .logo img {
            width: 60px;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <div class="page page-result animated fadeInUp active" style="padding-top: 0px">
            <div class="user">
                <img class="avatar" src="{{ $palette->user->detail->head_img or asset('images/no-avatar.png') }}">
                <div class="nickname">{{ $palette->user->detail->nickname or '' }}</div>
                <button type="button" class="pull-right btn btn-palette" onclick="window.location.href='{{ route('wechat.palette.index') }}'">
                    @if(Auth::id() == $palette->user->id)
                        重画一遍
                    @else
                        我也要玩
                    @endif
                </button>
            </div>
            <div class="background-1">
                <img src="{{ asset('images/palette/sky_'. $palette->sky_index .'.png') }}">
                <div class="canvas-image-area">
                    @if(!is_null($palette->month))
                        <img src="http://wj.qn.h-hy.com/{{ $palette->month }}">
                    @endif
                </div>
                <div class="description-area">
                    <p>{!! $palette->description !!}</p>

                </div>
            </div>
            <div class="background-2">
                <img src="http://wj.qn.h-hy.com/{{ $palette->source }}">
            </div>
            <div class="logo">
                <img src="{{ asset('images/logo_green.png') }}" alt="">
            </div>
        </div>
    </section>
@endsection

@section('javascript')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>


        function setShare(title)
        {
            var desc='你也来画一个吧。中秋快乐 :)';
            var link=window.location.href;
            var type='link';
            var imgUrl="{{ asset('images/palette/share.jpg') }}";
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
            setShare('月亮是我画的，我就是那个灵魂画手！');
        });
    </script>
@endsection