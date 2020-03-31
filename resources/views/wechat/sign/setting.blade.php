@extends('layouts.wechat')

@section('title', '个人中心')

@section('css')
    <style>
        .user-card {
            background-color: #96d3d1;
            background-image: url("{{ asset('images/sign/setting_banner.png') }}");
            background-repeat: no-repeat;
            background-size: 100%;
            background-position-y: 100%;
            min-height: 170px;
        }
        .user-card .user-avatar {
            padding-bottom: 10px;
            border-bottom: 1px solid #ffffff;
            padding-top: 30px;
            padding-left: 30px;
        }
        .user-card .user-avatar img {
            height: 80px;
            width: 80px;
            border: none;
            border-radius: 100%;
        }
        .user-card .nickname {
            padding: 10px 0;
            padding-left: 30px;
            color: #ffffff;
        }

        .flex-container {
            margin-top: 20px;
            display: flex;
            flex-direction: row;
        }
        .flex-item {
            flex: 1;
            text-align: center;
            color: #96d3d1;
            font-size: 16px;
        }
        .flex-item span {
            display: block;
            font-size: 20px;
            color: #000000;
            font-weight: bold;
        }

        section.container ul {
            margin-top: 10px;
            list-style: none;
            border-top: 1px solid #96d3d1;
        }
        section.container ul li {
            height: 40px;
            line-height: 40px;
            font-size: 15px;
            color: #5c5c5c;
            border-bottom: 1px solid #96d3d1;
            padding: 0 15px;
        }
        section.container ul li small {
            font-size: 13px;
            color: #adadad;
            float: right;
        }
        section.container ul li small:after {
            clear: both;
        }

        .card-list {
            position: fixed;
            top: 30%;
            left: 0;
            width: 100%;
            height: 60%;
            overflow-y: auto;
            background-color: #ffffff;
            border-radius: 30px 30px 0 0;
            z-index: 100;
            display: none;
        }
        .card-list .cancel-btn {
            text-align: center;
            min-height: 20px;
            padding: 10px;
        }

        .card-list ul {
            list-style: none;
            box-sizing: border-box;
        }

        .card-list ul li {
            display: block;
            background-image: url("{{ asset('images/sign/card_light.png') }}");
            background-size: 100% 100%;
            background-position: 0;
            background-repeat: no-repeat;
            min-height: 127px;
            padding: 10px;
            margin-bottom: 10px;
        }
        .card-list ul li.disable {
            background-image: url("{{ asset('images/sign/card_dim.png') }}");
            position: relative;
        }
        .card-list ul li.disable:after {
            content: '';
            position: absolute;
            background-image: url("{{ asset('images/sign/card_use.png') }}");
            background-size: 100% 100%;
            background-repeat: no-repeat;
            top: 45px;
            right: 70px;
            width: 120px;
            height: 60px;
        }
        .card-list ul li > div {
            padding-left: 38%;
        }
        .card-list ul li .top div {
            display: inline-block;
        }
        .card-list ul li .top .card-name {
            font-size: 54px;
            font-weight: bold;
            color: #ffffff;
        }
        .card-list ul li .top .card-button {
            padding-left: 10px;
            margin-top: -20px;
        }
        .card-button button {
            background-color: #41d5a4;
            color: #ffffff;
            border: none;
            outline: none;
            border-radius: 5px;
            padding: 5px;
            font-size: 18px;
        }
        .card-list ul li .bottom .card-description {
            font-size: 14px;
            font-weight: bold;
            color: #09a8aa;
            text-align: right;
        }
        .card-list ul li .bottom .date {
            font-size: 11px;
            color: #09a8aa;
            text-align: right;
        }
    </style>
@endsection

@section('content')
    @inject('sign_presenter', 'App\Presenters\SignPresenter')
    <section class="container">
        <div class="user-card">
            <div class="user-avatar">
                <img src="{{ $user_sign->user->detail->head_img }}">
            </div>
            <div class="nickname">{{ $user_sign->user->detail->nickname }}</div>
        </div>
        <div class="flex-container">
            <div class="flex-item">
                <span>持续时长</span>
                {{ $user_sign->duration_count }}天
            </div>
            <div class="flex-item">
                <span>奖金池</span>
                {{ $timer->reward or 0 }}元
            </div>
            <div class="flex-item">
                <span>早起值</span>
                {{ $user_sign->time_value }}
            </div>
        </div>
        <ul>
            <li>今日签到时间 <small>{{ $sign_presenter->today_time($today_time) }}</small></li>
            <li>总签到次数 <small>{{ $user_sign->total_count }}天</small></li>
            <li>当前排名 <small>{{ $sign_presenter->value_rank($value_rank) }}</small></li>
            <li id="card">卡券 <small>{{ $sign_presenter->can_use_card_count($user_sign->user->sign_cards) }}张 <i class="fa fa-angle-right"></i></small></li>
            <li id="reward">我的奖金 <small>{{ $user_sign->reward }}元 <i class="fa fa-angle-right"></i></small></li>
            <li>报名状态 <small>{{ $sign_presenter->applyStatus() }}</small></li>
            <li id="medal">勋章排行榜 <i class="fa fa-angle-right"></i></li>
            <li id="reward-rank">奖金排行榜 <i class="fa fa-angle-right"></i></li>
        </ul>
    </section>
    <div class="card-list">
        <div class="cancel-btn" style="color: #09a8aa;">点我隐藏</div>

        <ul>
            {{ $sign_presenter->card_list($card_list) }}
            {{--<li>--}}
                {{--<div class="top">--}}
                    {{--<div class="card-name">5折</div>--}}
                    {{--<div class="card-button">--}}
                        {{--<button type="button" class="use-btn">点击使用</button>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="bottom">--}}
                    {{--<div class="card-description">早起打卡报名五折代金券</div>--}}
                    {{--<div class="date">有效期限：2017年10月1号 - 10月10号</div>--}}
                {{--</div>--}}
            {{--</li>--}}
        </ul>
    </div>
    @include('wechat.sign._footer_bar')
@endsection

@section('javascript')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>


        $('#card').on('click', function () {
            $('.card-list').fadeIn(800);
        });
        $('.cancel-btn').on('touchstart', function () {
            $('.card-list').fadeOut(800);
        });
        $('#medal').on('click', function () {
            window.location.href = '{{ route('wechat.sign.rank') }}';
        });
        $('#reward').on('click', function () {
            window.location.href = '{{ route('wechat.sign.reward') }}';
        });
        $('#reward-rank').on('click', function () {
            window.location.href = '{{ route('wechat.sign.rank.reward') }}';
        });

        $('.use-btn').each(function () {
            var $this = $(this);
            $(this).on('click', function () {
                swal({
                    title: '确认使用该卡券？',
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonText: '取消',
                    confirmButtonText: '确认使用'
                }, function (confirm) {
                    if (confirm) {
                        $.ajax({
                            url: '{{ route('wechat.sign.card') }}',
                            type: 'post',
                            data: {
                                sign_card_id: $this.attr('card-id')
                            },
                            dataType: 'json',
                            success: function (data) {
                                if (data.code) {
                                    if (data.code == 1) {
                                        var config = data.data.config;
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
                                                    toastr.warning('用户取消支付，请重新点击支付按钮');
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
                                    if (typeof (data.data.redirect) != 'undefined') {
                                        $.timeoutGo(data.data.redirect, 1500);
                                    }
                                }
                            },
                            error: function (error){
                                alert(error.status);

                            }
                        });
                    }
                })
            })
        });

        function checkPay(out_trade_no)
        {
            $.ajax({
                url: '{{ route('wechat.sign.check.pay') }}',
                type: 'get',
                data: {
                    out_trade_no: out_trade_no
                },
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        if (data.code == 2) {
                            toastr.error(data.message);
                            $.timeoutGo(data.data.redirect, 1500);
                        } else {
                            setTimeout(checkPay(out_trade_no), 1000);
                        }
                    } else {
                        toastr.success(data.message);
                        $.timeoutGo(data.data.redirect, 1500);
                    }
                },
                error: function (error) {
                    alert(error.responseText);
                }
            })
        }
    </script>
@endsection