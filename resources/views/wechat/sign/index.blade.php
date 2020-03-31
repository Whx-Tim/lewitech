@extends('layouts.wechat')

@section('title', '早起打卡')

@section('css')
    <style>
        body {
            font-family: 'Lantinghei SC', '', sans-serif;
            width: 100%;
        }
        
        .sign-area {
            min-height: 200px;
            background-color: #80bcba;
            background-image: url("{{ asset('images/sign/banner.png') }}");
            background-position-y: 100%;
            background-size: 100%;
            background-repeat: no-repeat;
            position: relative;
            padding-top: 50px;
        }
        .sign-area button {
            text-align: center;
            font-size: 18px;
            border: none;
            outline: none;
            margin: 0 auto;
            background-color: #ffffff;
            color: #80bcba;
        }
        .sign-area button.signed-btn {
            border-radius: 100%;
            border-bottom: 10px solid #bad7d7;
            border-left: 2px solid #bad7d7;
            border-right: 2px solid #bad7d7;
            width: 150px;
            height: 150px;
            margin-left: calc((100% - 150px)/2);
            text-align: center;
        }
        .sign-area button.sign-btn {
            letter-spacing: .5em;
            padding: 3px;
            border-radius: 15px;
            border-bottom: 5px solid #bad7d7;
            width: 120px;
            margin-left: calc((100% - 120px)/2);
        }
        .sign-area p {
            text-align: center;
            color: #ffffff;
            margin-top: 5px;
        }

        .sign-date {
            font-family: 'Heiti SC', sans-serif;
            color: #8f9292;
            box-sizing: border-box;
            width: 94%;
            margin-left: 3%;
            margin-top: 3%;
            border: 2px solid #80bcba;
            border-radius: 5px;
            padding: 10px;
        }
        .sign-date .heading {
            font-size: 18px;
        }
        .sign-date small {
            color: #7ebcbb;
            float: right;
            font-size: 18px;
        }
        .sign-date small:after {
            clear: both;
        }
        .sign-date small img {
            width: 20px;
            vertical-align: middle;
        }
        .sign-date .date-box {
            margin-top: 20px;
        }
        .sign-date table {
            width: 100%;
            text-align: center;
        }
        .sign-date table tr th {
            width: 30px;
            height: 30px;
            margin: 0 1.5%;
            display: inline-block;
        }
        .sign-date table tr td {
            text-align: center;
            width: 30px;
            height: 30px;
            margin: 0 1.5%;
            border-radius: 100%;
            display: inline-block;
        }
        .sign-date table tr td.active {
            background-color: #80bcba;
            color: #ffffff;
        }

        .sign-list {
            border: 1px solid #80bcba;
            box-sizing: border-box;
            width: 94%;
            margin-left: 3%;
            margin-top: 3%;
        }
        .sign-list h4 {
            text-align: center;
            font-size: 25px;
            color: #ffffff;
            padding: 20px 0;
            background-color: #80bcba;
        }
        .sign-list .select-area {
            display: flex;
            flex-direction: row;
            border-bottom: 1px solid #80bcba;
            padding: 10px 0;
        }
        .sign-list .select-area .select-item {
            flex: 1;
            color: #c7c7c7;
            font-size: 20px;
            text-align: center;
        }
        .sign-list .select-area .select-item.active {
            color: #757575;
        }
        .sign-list ul {
            padding: 5px;
        }
        .sign-list ul li {
            padding: 5px 0;
            border-bottom: 1px solid rgb(240,240,240);
            display: flex;
            flex-direction: row;
        }
        .sign-list ul li.active {
            color: #94d3d2;
        }
        .sign-list ul li.active div.number {
            color: #94d3d2 !important;
        }
        .sign-list ul li div {
            flex: 1;
            text-align: center;
            line-height: 40px;
        }
        .sign-list ul li div.nickname {
            flex: 4;
        }
        .sign-list ul li div.duration {
            flex: 2;
        }

        .sign-list ul li div.number {
            font-style: italic;
            color: #f69045;
        }
        .sign-list .user-avatar {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 100%;
            vertical-align: middle;
        }

        #month-list, #total-list {
            display: none;
        }
    </style>
@endsection

@section('content')
<section class="container">
    <div class="sign-area">
        @if($is_sign == 1)
            <button type="button" class="signed-btn"><i class="fa fa-check fa-4x"></i><br>今日已打卡</button>
            <p>恭喜！你完成了今天的打卡！</p>
        @elseif($is_sign == 2)
            <button type="button" class="signed-btn"><i class="fa fa-close fa-4x"></i><br>今日漏签</button>
            <p>很抱歉~今日您漏签了</p>
        @else
            <button type="button" class="sign-btn">点此打卡</button>
        @endif
    </div>
    <div class="sign-date">
        <h4 class="heading">打卡日期 <small>已坚持{{ $user_sign->duration_count or 0 }}天 <img src="{{ asset('/images/sign/angle-bottom.png') }}" id="show-date" now="1"></small></h4>
        <div class="date-box">
            <table>
                <thead>
                    <tr>
                        <th>日</th>
                        <th>一</th>
                        <th>二</th>
                        <th>三</th>
                        <th>四</th>
                        <th>五</th>
                        <th>六</th>
                    </tr>
                </thead>
                <tbody>
                @inject('sign_presenter', 'App\Presenters\SignPresenter')
                    {!! $sign_presenter->signDateBox() !!}
                </tbody>
            </table>
        </div>
    </div>
    <div class="sign-list">
        <h4>早起排行榜</h4>
        <div class="select-area">
            <div class="select-item" target-list="month-list">月排行榜</div>
            <div class="select-item active" target-list="day-list">日排行榜</div>
            <div class="select-item" target-list="total-list">总排行榜</div>
        </div>
        <ul id="month-list">
            <li class="active">
                <div class="number">{{ $month_rank }}</div>
                <img src="{{ $user_sign->user->detail->head_img }}" class="user-avatar">
                <div class="nickname">{{ str_limit($user_sign->user->detail->nickname, 10) }}</div>
                <div class="duration">{{ $user_sign->month_count }}</div>
            </li>
            @foreach($month_list as $key => $item)
                <li>
                    <div class="number">{{ (int)($key+1) }}</div>
                    <img src="{{ $item->user->detail->head_img }}" class="user-avatar">
                    <div class="nickname">{{ str_limit($item->user->detail->nickname, 10) }}</div>
                    <div class="duration">{{ $item->month_count }}</div>
                </li>
            @endforeach
        </ul>
        <ul id="day-list">
            <li class="active">
                <div class="number">{{ $today_rank }}</div>
                <img src="{{ $user_sign->user->detail->head_img }}" class="user-avatar">
                <div class="nickname">{{ str_limit($user_sign->user->detail->nickname, 10) }}</div>
                <div class="duration">{{ $sign_presenter->today_time($today_time) }}</div>
            </li>
            @foreach($today_list as $key => $item)
                <li>
                    <div class="number">{{ (int)($key+1) }}</div>
                    <img src="{{ $item->user->detail->head_img }}" class="user-avatar">
                    <div class="nickname">{{ str_limit($item->user->detail->nickname, 10) }}</div>
                    <div class="duration">{{ $sign_presenter->today_time($item->today_time) }}</div>
                </li>
            @endforeach
        </ul>
        <ul id="total-list">
            <li class="active">
                <div class="number">{{ $total_rank }}</div>
                <img src="{{ $user_sign->user->detail->head_img }}" class="user-avatar">
                <div class="nickname">{{ str_limit($user_sign->user->detail->nickname, 10) }}</div>
                <div class="duration">{{ $user_sign->total_count }}</div>
            </li>
            @foreach($total_list as $key => $item)
                <li>
                    <div class="number">{{ (int)($key+1) }}</div>
                    <img src="{{ $item->user->detail->head_img }}" class="user-avatar">
                    <div class="nickname">{{ str_limit($item->user->detail->nickname, 10) }}</div>
                    <div class="duration">{{ $item->total_count }}</div>
                </li>
            @endforeach
        </ul>
    </div>
</section>
    @include('wechat.sign._footer_bar')
@endsection

@section('javascript')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        $('.sign-btn').on('click', function () {
            $.ajax({
                url: '{{ route('wechat.sign.clock') }}',
                type: 'get',
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        toastr.error(data.message);
                    } else {
                        toastr.success(data.message);
{{--                        $.timeoutGo('{{ route('wechat.sign.apply.confirm') }}', 500);--}}
                        $.timeoutGo('{{ route('wechat.sign.index') }}', 500);
//                        $.timeoutReload(1000);
                    }
                }
            })
        });

        $('#show-date').on('click', function () {
            if ($(this).attr('now') == 1) {
                $(this).attr('now', 0);
                $('tr[week]').fadeOut(500, function () {
                    $('tr[week={{ $sign_presenter->now_week() }}]').fadeIn(500);
                });
            } else {
                $(this).attr('now', 1);
                $('tr[week={{ $sign_presenter->now_week() }}]').fadeOut(500, function () {
                    $('tr[week]').fadeIn(500);
                });
            }
        });

        $('.select-item').each(function () {
            $(this).on('click', function () {
                $('.select-item').removeClass('active');
                $(this).addClass('active');
                var id = $(this).attr('target-list');
                $('.sign-list').children('ul').hide();
                $('#'+ id).fadeIn(300);
            })
        })

        function setShare(title)
        {
            var desc='中秋快乐 :)';
            var link=window.location.href;
            var type='link';
            var imgUrl="{{ asset('images/logo_white.png') }}";
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
            setShare('元气满满！我今天{{ $sign_presenter->today_time($today_time) }}就起床啦~你要跟我一起早起吗？');
        });
    </script>
@endsection