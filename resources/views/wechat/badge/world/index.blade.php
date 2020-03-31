@extends('layouts.wechat')

@section('title', '选择你支持的球队')

@section('css')
    <style>
        section.container {
            width: 100vw;
            height: 100vh;
            background-image: url("http://wj.qn.h-hy.com/images/lewitech/badge/world/index-bg.jpeg");
            background-size: 100%;
            background-repeat: no-repeat;
            background-position: bottom;
        }
        ._top-input-container {
            position: relative;
            width: 100%;
            height: 70vh;
            text-align: center;
        }
        ._top-input-container > img {
            width: 70%;
        }
        ._input-group {
            width: 100%;
            display: block;
            text-align: center;
        }
        ._input-group > ._input-box {
            background-color: white;
            text-align: left;
            display: inline-block;
            width: 90%;
            border-radius: 15px;
        }
        ._input-box > ._input-icon {
            box-sizing: border-box;
            width: 6vw;
            margin: 8px 18px;
            vertical-align: middle;
            margin-bottom: 10px;
        }
        ._input-box > input {
            padding: 5px;
            font-size: 16px;
            position: relative;
            border: none;
            padding-left: 5vw;
            max-width: 58vw;
            border-left: 1px solid rgb(200,200,200);
            border-radius: 0;
        }
        ._input-group > .btn-area {
            display: block;
            width: 100%;
            margin-top: 10px;
        }
        .btn-area {
            text-align: center;
        }
        .btn-area > .btn {
            width: 90%;
            background-color: #ff9629;
            color: white;
            font-weight: bolder;
            font-size: 16px;
            border: none;
            outline: none;
            padding: 15px;
            border-radius: 20px;
            margin-top: 5px;
        }
        .swiper-slide {
            text-align: center;
        }
        .swiper-slide > img {
            height: 80vh;
        }
        .swiper-slide > span {
            color: white;
            font-weight: bolder;
            font-size: 18px;
        }

        ._select-container {
            position: fixed;
            width: 100vw;
            height: 100vh;
            bottom: 0;
            left: 0;
            background-color: #282828;
            opacity: 0;
            padding: 20px;
            box-sizing: border-box;
            border-radius: 10px 10px 0 0;
            pointer-events: none;
            transform: translateY(100vh);
            transition: .8s all ease-in-out;
        }
        ._select-container.active {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
            transition: .8s all ease-in-out;
        }
        ._select-container > h4 {
            text-align: center;
            color: white;
        }


        /*._footer-container {*/
            /*height: 20vh;*/
            /*background-size: 100%;*/
            /*background-repeat: no-repeat;*/
            /*background-position: bottom;*/
            /*background-image: url("http://wj.qn.h-hy.com/images/lewitech/badge/index-footer-bg.png");*/
        /*}*/
    </style>
    <link rel="stylesheet" href="{{ url('css/plugins/swiper/swiper.min.css') }}">
@endsection

@section('content')
    <section class="container">
        <div class="_top-input-container">
            <img src="http://wj.qn.h-hy.com/images/lewitech/badge/world/title.png" alt="">
        </div>
        <div class="_input-group">
            <div class="_input-box">
                <img src="{{ asset('images/badge/world/icon.png') }}" class="_input-icon">
                <input type="text" class="_input-control" id="badge-picker" name="badge_name" readonly placeholder="请选择你支持的球队" onfocus="blur();">
                <input type="hidden" name="badge_id" value="">
            </div>
            <div class="btn-area">
                <button type="button" class="next-btn btn">点击生成世界杯头像</button>
            </div>
        </div>
        {{--<div class="_select-container">--}}
            {{--<h4>请左右滑动选择打CALL模式</h4>--}}
            {{--<div class="swiper-container">--}}
                {{--<div class="swiper-wrapper">--}}
                    {{--<div class="swiper-slide">--}}
                        {{--<span>正经打CALL</span><br>--}}
                        {{--<img src="http://wj.qn.h-hy.com/images/lewitech/badge/world/select_world_1.png">--}}
                    {{--</div>--}}
                    {{--<div class="swiper-slide">--}}
                        {{--<span>DIY打CALL</span><br>--}}
                        {{--<img src="http://wj.qn.h-hy.com/images/lewitech/badge/world/select_world_2.png">--}}
                    {{--</div>--}}
                    {{--<div class="swiper-slide">--}}
                        {{--<span>不正经打CALL</span><br>--}}
                        {{--<img src="http://wj.qn.h-hy.com/images/lewitech/badge/world/select_world_3.png">--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="swiper-pagination"></div>--}}
            {{--</div>--}}
            {{--<div class="btn-area">--}}
                {{--<button type="button" class="btn" id="submit-btn">确认</button>--}}
            {{--</div>--}}
        {{--</div>--}}
    </section>
@endsection

@section('javascript')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ url('/js/plugins/swiper/swiper.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            var type = 'diy';
            var mySwiper = new Swiper('.swiper-container',{
                freeMode: false,
                pagination: ".swiper-pagination",
                loop: true,
                height: 300,
//            width: window.innerWidth
                onSlideChangeEnd: function (swiper) {
                    switch (swiper.realIndex) {
                        case 0:
                            type = 'team';
                            break;
                        case 1:
                            type = 'diy';
                            break;
                        case 2:
                            type = 'default';
                            break;
                    }
                }
            });

            $('#badge-picker').on('click', function () {
                // $('._search-container').fadeIn(300);
                weui.picker([
                @foreach($badges as $badge)
                    {
                    label: '{{ $badge->name }}',
                    value: '{{ $badge->id }}'
                    },
                @endforeach
                ], {
                onConfirm: function (result) {
                    $('#badge-picker').val(result[0].label);
                    $('input[name=badge_id]').val(result[0].value);
                }
                })
            });

            // $('.next-btn').on('click', function () {
            //     var $name = $('input[name=badge_name]').val();
            //     if (!$name) {
            //         alert('请选择支持的球队');
            //         return ;
            //     }
            //     $('._select-container').addClass('active');
            // });

            $('.next-btn').on('click', function () {
                var $name = $('input[name=badge_name]').val();
                if (!$name) {
                    alert('请选择支持的球队');
                    return ;
                }
                $.ajax({
                    url: '{{ route('wechat.badge.world.combine') }}',
                    type: 'post',
                    data: {
                        badge_id: $('input[name=badge_id]').val(),
                        type: type
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data.code) {
                            toastr.error(data.message);
                        } else {
                            toastr.success(data.message);
                            $.timeoutGo(data.data.redirect);
                        }
                    },
                    error: function () {
                        toastr.error('目前参与人数过多，请稍后重试');
                    }
                })
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
        });
    </script>
@endsection