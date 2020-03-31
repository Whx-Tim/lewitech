@extends('layouts.wechat')

@section('title', '我的捐赠证书')

@section('css')
    <style>
        body, html {
            width: 100%;
            height: 100%;
        }

        section.container {
            width: 100%;
            height: 100%;
            background-image: url("{{ asset('images/sign/cert.png') }}");
            background-size: 100% 100%;
            background-repeat: no-repeat;
            position: relative;
        }

        .introduction {
            position: absolute;
            top: 25%;
            left: 0;
            padding: 10px 40px;
        }
        .introduction p {
            text-align: left;
            color: #353535;
            letter-spacing: .2em;
            line-height: 30px;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <div class="introduction">
            @if(!empty($signDonate))
                <p>亲爱的 {{ empty($signDonate->name) ? $user->detail->nickname : $signDonate->name }}:</p>
    {{--            <p>感谢您对公益爱心伞项目的支持，您是第 {{ $user->signDonate->id }}位捐赠人，有了您的捐赠，将有更多的市民能够在右需要的时候得到帮助，解决出行烦恼。公益就在我们的身边，让我们一起支持公益吧！</p>--}}
                <p>感谢您对公益爱心伞项目的支持，您是第 {{ $signDonate->id or 1 }} 位捐赠人，有了您的捐赠，将有更多的市民能够在有需要的时候得到帮助，解决出行烦恼。公益就在我们的身边，让我们一起支持公益吧！</p>
            @else
                <p>您查看的用户尚未进行捐赠</p>
            @endif
        </div>
    </section>
@endsection

@section('javascript')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        function setShare(title)
        {
            var desc=title;
            var link=window.location.href;
            var type='link';
            var imgUrl="{{ asset('images/logo_transparent.png') }}";
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
            setShare('我刚捐赠了1把公益爱心伞！');
        });
    </script>
@endsection