@extends('layouts.wechat')

@section('title', '收费报名')

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
            box-sizing: border-box;
            font-size: 15px;
        }

        .top {
            width: 100%;
            min-height: 410px;
            background-image: url("{{ asset('images/sign/apply-background.png') }}");
            background-size: 100%;
            background-repeat: no-repeat;
        }
        .top .money {
            padding-top: 50px;
            color: #323232;
        }
        .money > p {
            font-size: 16px;
            font-weight: bolder;
            text-align: center;
        }
        .money > h4 {
            text-align: center;
            font-size: 40px;
        }
        .regulation {
            text-align: center;
        }
        .regulation > a {
            text-decoration: none;
            color: #323232;
        }

        .apply-btn {
            border: none;
            outline: none;
            color: #ffffff;
            background-color: #47d1c1;
            font-weight: bolder;
            font-size: 20px;
            width: 90%;
            margin-left: 5%;
            border-radius: 15px;
            text-align: center;
            padding: 10px 0;
        }
        .free-btn {
            border: none;
            outline: none;
            background-color: #ffffff;
            color: #47d1c1;
            font-size: 16px;
            width: 40%;
            margin-left: 30%;
            text-align: center;
            padding: 10px 0;
        }
        .date {
            margin-top: 5px;
            color: #879898;
            text-align: center;
            padding-bottom: 100px;
        }

        .page-regulation {
            background-color: rgba(0,0,0, .6);
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            display: none;
            position: fixed;
            z-index: 99;
        }

        .mask {
            background-color: #ffffff;
            position: fixed;
            width: 90%;
            height: 80%;
            top: 10%;
            left: 5%;
            border: none;
            border-radius: 30px;
        }

        .page-regulation  .regulation-content {
            height: 90%;
            overflow-y: auto;
            padding: 15px;
        }
        .regulation-content > p {
            color: rgb(150,150,150);
        }
        .page-regulation  .close-btn {
            position: absolute;
            bottom: 0;
            left: 0;
            border: none;
            outline: none;
            color: #ffffff;
            background-color: #47d1c1;
            font-weight: bolder;
            font-size: 20px;
            width: 100%;
            text-align: center;
            padding: 10px 0;
            border-radius: 0 0 30px 30px;
        }

    </style>
@endsection

@section('content')
    <section class="container">
        <div class="top">
            <div class="money">
                <p>早起可瓜分总金额（元）</p>
                <h4>&#65509;&nbsp;{{ $now_reward }}</h4>
                <div class="regulation">
                    <a href="javascript:;" id="regulation-btn">挑战规则  <i class="fa fa-angle-right"></i></a>
                </div>
            </div>
        </div>
        <div class="apply">
            <button type="button" class="apply-btn">收费报名</button>
        </div>
        <div class="date">
            *本轮打卡起止时间12月1日-12月30日
        </div>
    </section>
    <div class="page-regulation">
        <div class="mask">
            <div class="regulation-content">
                <p>“用押金来督促自己早起，完成即返另赢赏”的机制每天一元钱，坚持一整个月的签到，坚持完成所有签到即返还押金，漏签则扣除押金进入奖金池。全部坚持下来的朋友，不仅返还押金，还可瓜分未坚持打卡用户的押金。</p>
                <p>每天打卡时间：早上5:00-10:00</p>
                <p>打卡起止日期：2017年12月1日-12月31日</p>
                <p>参与报名金额：30元/人/月</p>
                <p>奖金提现日期：2017年12月31日</p>
                <p>附录：</p>
                <p>1.新手礼包：赠送五折代金券1张+补签券1张</p>
                <p>2.持续打卡老用户礼包：九折优惠</p>
                <p>（均可在个人中心查看）</p>
                <p>注：最终解释权归乐微科技所有</p>
            </div>
            <button type="button" class="close-btn">确定</button>
        </div>
    </div>
    {{--@include('wechat.sign._footer_bar')--}}
@endsection

@section('javascript')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        alert('报名已截止');
        $('#regulation-btn').on('click', function () {
            $('.page-regulation').fadeIn(300);
        });
        $('.close-btn').on('click', function () {
            $('.page-regulation').fadeOut(300);
        });

        $('.apply-btn').on('click', function () {
            $.ajax({
            url: '{{ route('wechat.sign.apply.free2deposit') }}',
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
        });

        function checkPay(out_trade_no)
        {
            $.ajax({
                url: '{{ route('wechat.sign.check.free2deposit') }}',
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

        wx.config(<?php echo $js->config(array('hideAllNonBaseMenuItem'), false) ?>);
        wx.ready(function(){
            wx.hideAllNonBaseMenuItem();
        });
    </script>
@endsection