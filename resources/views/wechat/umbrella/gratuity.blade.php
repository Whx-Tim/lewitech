@extends('layouts.wechat')

@section('title', '打赏')

@section('css')
    <style>
        @keyframes scrollText {
            from {
                transform: translateX(0);
            }
            to {
                transform: translateX(-{{ ($deal_count)*80+3 }}vw);
            }
        }

        .banner > img {
            display: block;
            width: 100%;
        }
        .broadcast {
            padding: 5px;
            padding-bottom: 1px;
            background-color: #7a7a7a;
            color: #ffffff;
        }
        .broadcast > img {
            display: inline-block;
            width: 40px;

        }
        .slide-area {
            position: relative;
            display: inline-block;
            width: 80vw;
            height: 20px;
            overflow: hidden;
            font-size: 15px;
            vertical-align: super;
            /*float: left;*/
        }
        .slide-box {
            position: absolute;
            top: 0;
            left: 0;
            width: {{ ($deal_count+2)*80 }}vw;
            animation: scrollText {{ $deal_count*5 }}s infinite linear;
        }
        .slide-box > p {
            height: 30px;
            width: 80vw;
            overflow: hidden;
            display: inline-block;
            text-align: center;
        }

        .donate-area h4 {
            text-align: center;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        .input-group {
            text-align: center;
        }

        .input-group > label {
            padding: 5px;
            font-size: 14px;
        }
        .input-group > input {
            padding: 5px;
            font-size: 14px;
            width: 50vw;
        }

        .select-flex {
            display: flex;
            flex-direction: row;
            margin-top: 20px;
        }

        .select-label {
            flex: 1;
            text-align: center;
        }
        .select-label span {
            background-color: #dbdbdb;
            padding: 10px;
        }
        /*.select-label > span:before {*/
            /*content: '￥'*/
        /*}*/
        .select-label > span:after {
            content: '元'
        }

        .submit-btn {
            display: inline-block;
            background-color: #48acc8;
            padding: 10px;
            width: 94%;
            margin-left: 3%;
            margin-top: 30px;
            font-size: 16px;
            border: none;
            outline: none;
            color: #ffffff;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <div class="banner">
            <img src="{{ asset('images/umbrella/gratuity/banner.jpeg') }}">
        </div>
        {{--<div class="broadcast">--}}
            {{--<img src="{{ asset('images/umbrella/gratuity/icon-broadcast.png') }}">--}}
            {{--<div class="slide-area">--}}
                {{--<div class="slide-box">--}}
                    {{--@foreach($deals as $deal)--}}
                        {{--<p>{{ $deal->user->detail->nickname }} 打赏了 {{ $deal->money }}元</p>--}}
                    {{--@endforeach--}}
                        {{--<p>{{ $first_deal->user->detail->nickname }} 打赏了 {{ $first_deal->money }}元</p>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
        <div class="donate-area">
            <h4>您也来为深圳市民的便利出行贡献一份力吧</h4>
            <form>
                <div class="input-group">
                    <label>您将捐赠：</label>
                    <input type="text" name="money" placeholder="在此输入您的打赏金额">&nbsp;元
                </div>
                <div class="select-flex">
                    <div class="select-label"><span>2</span></div>
                    <div class="select-label"><span>5</span></div>
                    <div class="select-label"><span>10</span></div>
                </div>
            </form>
            <button type="button" class="submit-btn">确定</button>
        </div>
    </section>
@endsection

@section('javascript')
    <script>
        $('.select-label').each(function () {
            var $this = $(this);
            $this.on('click', function () {
                $('[name=money]').val($this.text());
            });
        });

        $('.submit-btn').on('click', function () {
            var formData = $('form').serialize();
            $.ajax({
                url: '{{ route('wechat.pay.response.umbrella.gratuity') }}',
                type: 'post',
                data: formData,
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
                        $('.page-info').fadeIn(300);
                    }
                }
            });
        });

        function checkPay(out_trade_no)
        {
            $.ajax({
                url: '{{ route('wechat.pay.response.umbrella.check') }}',
                type: 'get',
                data: {
                    out_trade_no: out_trade_no
                },
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        if (data.code == 2) {
                            toastr.error(data.message);
                            if (typeof (data.data.redirect) != 'undefined') {
                                $.timeoutGo(data.data.redirect, 1500);
                            } else {
                                $.timeoutReload();
                            }

                        } else {
                            setTimeout(checkPay(out_trade_no), 1000);
                        }
                    } else {
                        toastr.success(data.message);
                        alert('感谢您的支持!');
                       if (typeof (data.data.redirect) != 'undefined') {
                           $.timeoutGo(data.data.redirect, 1500);
                       } else {
                           $.timeoutReload();
                       }
                    }
                },
                error: function (error) {
                    alert(error.responseText);
                }
            })
        }
    </script>
@endsection