@extends('layouts.wechat')

@section('title', '早起打卡')

@section('css')
    <style>
        body {
            padding: 10px;
            padding-bottom: 0;
            background-color: #828284;
        }

        section.container {
            background-color: white;
            border-radius: 5px;
            text-align: center;
            position: relative;
            min-height: 95vh;
        }

        img.title {
            width: 90%;
        }

        .sign-box {
            width: 80%;
            text-align: center;
            border-radius: 10px;
            border: 2px solid black;
            padding: 10px;
            display: inline-block;
        }
        .sign-box > img {
            width: 70%;
        }
        .sign-box > h5 {
            color: #828284;
            font-size: 18px;
            margin-top: 5px;
            font-weight: bolder;
        }
        .sign-box > h4 {
            font-size: 25px;
        }
        .sign-box > h4 > span {
            color: #fe8020;
        }
        .sign-box > p {
            font-size: 12px;
            color: #828284;
        }
        .sign-box > button {
            width: 70%;
            background-color: #ffd258;
            color: black;
            border-radius: 15px;
            border: 2px solid black;
            margin-top: 5px;
            font-size: 15px;
            font-weight: bolder;
            padding: 5px;
        }

        .rank-area {
            width: 95%;
            border-radius: 5px 5px;
            border: 2px solid black;
            display: inline-block;
            margin-top: 20px;
        }
        .rank-area > .title {
            font-size: 25px;
            color: white;
            background-color: #ff7d2e;
            padding: 10px;
            text-align: center;
            border-bottom: 2px solid black;
        }
        ul.rank-list {
            list-style: none;
        }
        ul.rank-list > li {
            padding: 10px;
            display: flex;
            flex-direction: row;
        }
        ul.rank-list > li > span.avatar > img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            vertical-align: middle;
        }
        ul.rank-list > li > span {
            text-align: center;
            flex: 1;
            vertical-align: middle;
            font-size: 13px;
            line-height: 60px;
        }
        ul.rank-list > li > span.avatar {
            flex: 3;
        }
        ul.rank-list > li > span.username {
            flex: 4;
        }
        ul.rank-list > li > span.day {
            flex: 2;
        }

        .user-btn {
            position: fixed;
            right: 0;
            top: 15vh;
            height: 40px;
            width: 18vw;
            background-color: #ffd258;
            text-align: left;
            padding-left: 20px;
            padding-top: 8px;
            border-radius: 40px 0 0 40px;
            box-sizing: border-box;
        }
        .user-btn > img {
            height: 24px;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <div class="user-btn">
            <img src="{{ asset('images/day_sign/user.png') }}" alt="">
        </div>
        <div class="info-area">
            <img src="{{ asset('images/day_sign/title.png') }}" alt="" class="title">
            <div class="sign-box">
                <img src="{{ asset('images/day_sign/sign_banner.png') }}" alt="">
                <h5>今日可瓜分金额</h5>
                <h4><span>￥{{ $reward }}</span>元</h4>
                <p>5:00 -- 8:30 前打卡可成功瓜分金额</p>
                @if($is_apply == false)
                    <button type="button" id="apply-btn">点击参与报名</button>
                @else
                    @if($is_sign == false)
                        <button type="button" id="sign-btn">点击打卡</button>
                    @else
                        <button type="button">已打卡</button>
                    @endif
                @endif
            </div>
        </div>
        <div class="rank-area">
            <h4 class="title">早起排行榜</h4>
            <ul class="rank-list">
                @foreach($sign_users as $key => $user)
                    <li>
                        <span class="id">{{ $key+1 }}</span>
                        <span class="avatar"><img src="{{ $user->user->detail->head_img }}" alt=""></span>
                        <span class="username">{{ str_limit($user->user->detail->nickname, 15) }}</span>
                        <span class="day">{{ \Carbon\Carbon::parse($user->time)->format('H:i') }}</span>
                    </li>
                @endforeach
                {{--<li>--}}
                    {{--<span class="id">1</span>--}}
                    {{--<span class="avatar"><img src="{{ asset('images/no-avatar.png') }}" alt=""></span>--}}
                    {{--<span class="username">XIN</span>--}}
                    {{--<span class="day">100天</span>--}}
                {{--</li>--}}
                {{--<li>--}}
                    {{--<span class="id">1</span>--}}
                    {{--<span class="avatar"><img src="{{ asset('images/no-avatar.png') }}" alt=""></span>--}}
                    {{--<span class="username">XIN</span>--}}
                    {{--<span class="day">100天</span>--}}
                {{--</li>--}}
                {{--<li>--}}
                    {{--<span class="id">1</span>--}}
                    {{--<span class="avatar"><img src="{{ asset('images/no-avatar.png') }}" alt=""></span>--}}
                    {{--<span class="username">XIN</span>--}}
                    {{--<span class="day">100天</span>--}}
                {{--</li>--}}
            </ul>
        </div>
    </section>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            $('.user-btn').on('click', function () {
                window.location.href = '{{ route('wechat.daysign.setting') }}';
            });
            $('#sign-btn').on('click', function () {
                $.ajax({
                    url: '{{ route('wechat.daysign.sign') }}',
                    type: 'get',
                    dataType: 'json',
                    success: function (data) {
                        if (data.code) {
                            toastr.error(data.message)
                        } else {
                            toastr.success(data.message);
                            $.timeoutReload(1000);
                        }
                    },
                    error: function (error) {
                        toastr.error('系统繁忙，请稍后重试');
                    }
                })
            });
            $('#apply-btn').on('click', function () {
                $.ajax({
                    url: '{{ route('wechat.daysign.order') }}',
                    type: 'post',
                    dataType: 'json',
                    success: function (data) {
                        if (data.code) {
                            if (data.code == 1) {
                                var config = data.data.config;
                                console.log(config);
                                WeixinJSBridge.invoke(
                                    'getBrandWCPayRequest', {
                                        "appId" : config.appId,     //公众号名称，由商户传入
                                        "timeStamp": config.timestamp,
                                        "nonceStr" : config.nonceStr, //随机串
                                        "package" : config.package,
                                        "signType" :config.signType,//微信签名方式:
                                        "paySign" : config.paySign //微信签名
                                    },
                                    function(res){
                                        if (res.err_msg == "get_brand_wcpay_request:ok") {
                                            setTimeout(checkPay(config.out_trade_no), 1000);
                                        }else if(res.err_msg == "get_brand_wcpay_request:cancel" ) {
                                            toastr.warning('取消支付，请重新点击支付按钮');
                                        }else{
                                            toastr.error('发起支付失败：'+(res.errMsg?res.errMsg:'')+(res.err_msg?res.err_msg:'')+(res.err_desc?res.err_desc:''));
                                        }
                                    }
                                );

                            } else {
                                toastr.error(data.message);
                            }
                        } else {
                            toastr.success(data.message);
                            // $.timeoutGo(data.data.redirect, 800);
                        }
                    }
                });
            });

            function checkPay(out_trade_no)
            {
                $.ajax({
                    url: '{{ route('wechat.daysign.order.check') }}',
                    type: 'get',
                    data: {
                        out_trade_no: out_trade_no
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data.code) {
                            if (data.code == 2) {
                                toastr.error(data.message);
                                $.timeoutReload(1000);
                                // $.timeoutGo(data.data.redirect, 1500);
                            } else {
                                setTimeout(checkPay(out_trade_no), 1000);
                            }
                        } else {
                            toastr.success(data.message);
                            $.timeoutReload(1000);
                            // $.timeoutGo(data.data.redirect, 1500);
                        }
                    },
                    error: function (error) {
                        alert(error.responseText);
                    }
                })
            }
        })
    </script>
@endsection