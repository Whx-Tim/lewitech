@extends('layouts.wechat')

@section('title', '签到报名')

@section('css')
    <style>
        html,body {
            font-family: 'Lantinghei SC', sans-serif;
            width: 100%;
            height: 100%;
        }

        section.container {
            width: 100%;
            height: 100%;
            padding: 20px;
            box-sizing: border-box;
            font-size: 15px;
        }

        .top .heading {
            text-align: center;
            color: #e1e1e1;
        }

        .body .heading {
            text-align: center;
            margin-right: 10px;
            margin-left: 10px;
            border-bottom: 1px solid black;
            padding-top: 10px;
            padding-bottom: 15px;
            color: #3e3d3d;
        }

        .body p {
            position: relative;
            padding: 40px 30px;
        }
        .body p:before {
            content: "“";
            font-weight: bold;
            font-size: 60px;
            position: absolute;
            top: 0;
            left: -5px;
            color: #e5e5e5;
        }

        .body p b {
            display: block;
        }
        .body p span {
            display: block;
            color: #ababab;
        }
        .body p a {
            display: block;
            text-align: right;
            color: #7ebcbb;
            text-decoration: none;
            margin-top: 10px;
            font-weight: 600;
        }
        .body p a:focus,
        .body p a:active,
        .body p a:hover {
            color: #689e9d;

        }
        .body p a img {
            height: 18px;
            vertical-align: sub;
        }

        .footer {
            margin-top: 20px;
        }

        .footer .checkbox {
            font-size: 13px;
            color: #7ebcbb;
            letter-spacing: .1em;
        }

        .footer .select-apply {
            margin-top: 10px;
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        .footer .select-apply .select-item {
            width: 50%;
            min-height: 100px;
            border: 1px solid #d4d4d4;
            color: #d4d4d4;
        }
        .footer .select-apply .select-item.active {
            color: #4d4e4f;
            border: 1px solid #7ebcbb;
        }

        .footer .select-apply .select-item .heading {
            display: block;
            padding-left: 5px;
            padding-top: 3px;
            position: relative;
            font-size: 12px;
        }
        .footer .select-apply .select-item .heading:after {
            content: '';
            position: absolute;
            top: 2px;
            right: 5px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            border: 1px solid #d4d4d4;
        }
        .footer .select-apply .select-item.active .heading:after {
            background-image: url("/images/sign/apply-select.png");
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }
        .footer .select-apply .select-item .body h3 {
            padding-top: 20px;
            text-align: center;
        }

        .normal-btn {
            margin-top: 30px;
            width: 100%;
            background-color: #3ed2c2;
            color: #ffffff;
            padding: 10px;
            font-size: 15px;
            border-radius: 25px;
            box-sizing: border-box;
            border: none;
            outline: none;
        }


    </style>
@endsection

@section('content')
    <section class="container">
        <div class="top">
            <h4 class="heading">CLOCK PUNCH</h4>
        </div>
        <div class="body">
            <h3 class="heading">早起打卡</h3>
            <p>
                <b>你离更好的自己只差30天</b>
                <span>每日坚持早起签到，高效的一天从早起开始，坚持完成一个月的签到，返还押金的同时还可以拿到奖金！</span>
{{--                <a href="#" >去报名普通共读 <img src="{{ asset('/images/sign/apply.png') }}"></a>--}}
            </p>
        </div>
        <div class="footer">
            {{--<div class="checkbox">--}}
                {{--<input type="checkbox" name="user-agreement" style="vertical-align: middle;">勾选即表示同意<a href="#">《用户协议》</a>--}}
            {{--</div>--}}
            <div class="select-apply">
                <div class="select-item active" style="margin-right: 5px">
                    <div class="heading">方式一:</div>
                    <div class="body">
                        <h3>付费报名<br>30元/本期</h3>
                    </div>
                </div>
                <div class="select-item">
                    <div class="heading">方式二:</div>
                    <div class="body">
                        <h3>免费报名<br>无奖金</h3>
                    </div>
                </div>
            </div>
            <button type="button" class="normal-btn">立即报名</button>
        </div>
    </section>
    @include('wechat.sign._footer_bar')
@endsection

@section('javascript')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        var is_free = 0;
        $('.select-item').each(function (index) {
            var $this = $(this);
            $this.on('click', function () {
                is_free = index;
                console.log(is_free);
                $('.select-item').each(function () {
                    $(this).removeClass('active');
                });
                $this.addClass('active')
            })
        });

        $('.normal-btn').on('click', function () {
            @if($is_have)
            swal({
                title: '您有报名优惠卡券可以使用，点击个人中心->卡券进行使用报名',
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: '不使用',
                confirmButtonText: '确认使用'
            }, function (confirm) {
                if (confirm) {
                    window.location.href = '{{ route('wechat.sign.setting') }}';
                } else {
                    $.ajax({
                        url: '{{ route('wechat.sign.apply') }}',
                        type: 'post',
                        data: {
                            is_free: is_free
                        },
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
                                $.timeoutGo(data.data.redirect, 800);
                            }
                        }
                    });
                }
            });
            @else
                $.ajax({
                url: '{{ route('wechat.sign.apply') }}',
                type: 'post',
                data: {
                    is_free: is_free
                },
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
                        $.timeoutGo(data.data.redirect, 800);
                    }
                }
            });
            @endif

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