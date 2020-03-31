@extends('layouts.wechat')

@section('title', '长按保存图片')

@section('css')
    <style>
        section.container {
            width: 100vw;
        }
        section.container > img {
            width: 100%;
        }

        .btn-area {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .btn {
            width: 60%;
            background-color: #ff9629;
            font-size: 13px;
            border: none;
            border-radius: 20px;
            color: white;
            outline: none;
            padding: 10px 0;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <img src="{{ asset($share) }}" alt="">
        <div class="btn-area">
            <button type="button" class="btn" id="restart-btn">重新选择其他模式</button>
        </div>

    </section>
@endsection

@section('javascript')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        $(document).ready(function () {
            $('#restart-btn').on('click', function () {
                window.location.href = '{{ route('wechat.badge.world.index') }}';
            });
            function setShare(title)
            {
                var desc=title;
                var link='{{ $link }}';
                var type='link';
                var imgUrl="http://wj.qn.h-hy.com/images/lewitech/badge/world/world_banner.jpeg";
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
                setShare('好玩！自定义生成世界杯头像和趣图，你也来试试！');
            });
        })
    </script>
@endsection