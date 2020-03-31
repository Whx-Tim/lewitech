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
    </style>
@endsection

@section('content')
    <section class="container">
        <img src="{{ asset($share) }}" alt="">
    </section>
@endsection

@section('javascript')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        $(document).ready(function () {
            function setShare(title)
            {
                var desc=title;
                var link='{{ route('wechat.badge.index') }}';
                var type='link';
                var imgUrl="{{ asset($user_avatar) }}";
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
                setShare('{{ Auth::user()->detail->nickname }}邀您一起生成母校校徽头像，快来参加吧~');
            });
        })
    </script>
@endsection