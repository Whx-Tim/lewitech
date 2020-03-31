@extends('layouts.wechat')

@section('title', '选择大学')

@section('css')
    <style>
        section.container {
            width: 100vw;
            height: 100vh;
            background-color: #259ae9;
            background: -webkit-gradient(linear, -50 -50, 0 bottom, from(white), to(#259ae9));;
        }
        ._top-input-container {
            position: relative;
            width: 100%;
            height: 80vh;
            background-size: 100%;
            background-position-y: -150%;
            background-repeat: no-repeat;
            background-image: url("http://wj.qn.h-hy.com/images/lewitech/badge/index-top-bg.png");
        }
        ._top-input-container > ._input-group {
            position: absolute;
            width: 100%;
            display: block;
            left: 0;
            bottom: 0;
            text-align: center;
        }
        ._input-group > ._input-box {
            background-color: white;
            text-align: left;
            display: inline-block;
            width: 80%;
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
        .btn-area > .btn {
            width: 80%;
            background-color: #075ebb;
            color: white;
            font-size: 16px;
            border: none;
            outline: none;
            padding: 15px;
            border-radius: 20px;
            margin-top: 5px;
        }

        ._footer-container {
            height: 20vh;
            background-size: 100%;
            background-repeat: no-repeat;
            background-position: bottom;
            background-image: url("http://wj.qn.h-hy.com/images/lewitech/badge/index-footer-bg.png");
        }

        ._search-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 10;
            width: 100vw;
            height: 100vh;
            background-color: rgba(100,100,100, .8);
        }
        ._search-input-box {
            display: block;
            text-align: center;
            background-color: white;
            padding: 10px 0;
        }
        ._search-input-box > input {
            font-size: 14px;
            padding: 5px;
            outline: none;
            border-radius: 15px;
            display: inline-block;
            width: 60vw;
            border: 1px solid rgb(180,180,180);
        }
        ._search-input-box > button {
            display: inline-block;
            width: 20vw;
            font-size: 14px;
            padding: 5px;
            color: white;
            background-color: #075ebb;
            border: none;
            border-radius: 5px;
        }
        ._search-show-area {
            display: block;
            max-height: 70vh;
            overflow-y: auto;
        }
        ._search-school-list {
            background-color: white;
            list-style: none;
        }
        ._search-school-list > li {
            padding: 10px 10vw;
            font-size: 14px;
            border: 1px solid #f1f1f1;
        }
        ._search-school-list > li.active {
            color: white;
            background-color: #075ebb;
        }
        ._search-btn-area {
            position: absolute;
            z-index: 11;
            bottom: 0;
            left: 0;
            min-height: 8vh;
            width: 100vw;
            display: block;
            text-align: center;
        }
        .search-cancel,
        .search-ok{
            font-size: 14px;
            font-weight: bolder;
            border: none;
            border-radius: 15px;
            padding: 10px 0;
            outline: none;
            width: 80vw;
            color: white;
            margin-bottom: 5px;
        }
        .search-cancel {
            background-color: orangered;
            margin-right: 10px;
        }
        .search-ok {
            background-color: #1fb922;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <div class="_top-input-container">
            <div class="_input-group">
                <div class="_input-box">
                    <img src="{{ asset('images/badge/icon-school.png') }}" class="_input-icon">
                    <input type="text" class="_input-control" id="school-picker" name="school_name" readonly placeholder="请选择你的学校" onfocus="blur();">
                </div>
                <div class="btn-area">
                    <button type="button" class="next-btn btn">下一步</button>
                </div>
            </div>
        </div>
        <div class="_footer-container">
            <img src="" alt="">
            <img src="" alt="">
            <img src="" alt="">
        </div>
    </section>
    <section class="_search-container">
        <div class="_search-input-box">
            <input type="text" name="search" placeholder="请输入大学的关键字">
            <button type="button" id="search">搜索</button>
        </div>
        <div class="_search-show-area">
            <ul class="_search-school-list">
            </ul>
        </div>
        <div class="_search-btn-area">
            <button type="button" class="search-cancel">取消</button>
            {{--<button type="button" class="search-ok">确认</button>--}}
        </div>
    </section>
@endsection

@section('javascript')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        $(document).ready(function () {
            $('#school-picker').on('click', function () {
                $('._search-container').fadeIn(300);
                {{--weui.picker([--}}
                    {{--@foreach($schools as $school)--}}
                    {{--{--}}
                        {{--label: '{{ $school->name }}',--}}
                        {{--value: '{{ $school->id }}'--}}
                    {{--},--}}
                    {{--@endforeach--}}
                {{--], {--}}
                    {{--onConfirm: function (result) {--}}
                        {{--$('#school-picker').val(result[0].label);--}}
                    {{--}--}}
                {{--})--}}
            });

            $('#search').on('click', function () {
                $.ajax({
                    url: '{{ route('wechat.badge.search') }}',
                    type: 'get',
                    data: {
                        search: $('input[name=search]').val()
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data.code) {
                            toastr.error(data.message);
                            // if (data.code == 1) {
                            //     $('._search-school-list').html('<p>没有找到相关</p>')
                            // }
                        }  else {
                            var result = data.data.result;
                            var html = '';
                            for(var i in result) {
                                html += '<li>'+ result[i] +'</li>';
                            }
                            $('._search-school-list').html(html);
                        }
                    },
                    error: function (error) {
                        toastr.error('系统目前使用人数过多，请稍后重试');
                    }
                })
            });

            $('.search-ok').on('click', function () {
                $('#school-picker').val($('input[name=search]').val());
                $('._search-container').fadeOut(300);
            });

            $('.search-cancel').on('click', function () {
                $('._search-container').fadeOut(300);
            });

            $('._search-school-list').delegate('li', 'click', function () {
                var $this = $(this);
                $('._search-school-list').children('li').removeClass('active');
                $this.addClass('active');
                $('input[name=search]').val($this.html());
                $('#school-picker').val($this.html());
                $('._search-container').fadeOut(300);
            });

            $('.next-btn').on('click', function () {
                $.ajax({
                    url: '{{ route('wechat.badge.combine') }}',
                    type: 'post',
                    data: {
                        school_name: $('[name=school_name]').val()
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
                var link='{{ route('wechat.badge.index') }}';
                var type='link';
                var imgUrl="http://wj.qn.h-hy.com/images/lewitech/badge/index-top-bg.png";
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
        });
    </script>
@endsection