@extends('layouts.wechat')

@section('title', '共享雨伞捐赠')

@section('css')
    <style>
        body,html {
            width: 100%;
            height: 100%;
        }

        section.container {
            width: 100%;
            height: 100%;
            background-image: url("{{ asset('images/sign/donate.png') }}");
            background-size: 100% 100%;
            background-repeat: no-repeat;
            position: relative;
        }

        h2 {
           position: absolute;
            top: 15px;
            left: 0;
            color: #ffffff;
            width: 100%;
            display: block;
            text-align: center;
        }

        .introduction {
            position: absolute;
            top: 40%;
            left: 0;
            color: #353535;
            display: block;
        }
        .introduction > h4 {
            text-align: center;
            display: block;
            width: 100%;
            font-weight: bolder;
        }
        .introduction > p {
            text-indent: 2em;
            width: 100%;
            padding: 10px 15px;
            box-sizing: border-box;
        }

        .donate-btn {
            position: absolute;
            left: 5%;
            bottom: 10%;
            display: inline-block;
            text-decoration: none;
            width: 90%;
            padding: 10px;
            text-align: center;
            background-color: #60c4c1;
            color: #ffffff;
            margin-top: 40px;
            box-sizing: border-box;
            border-radius: 20px;
            border: none;
            font-size: 18px;
        }

        .page-info {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0, .6);
        }
        .page-info > form {
            display: inline-block;
            position: absolute;
            top: 10%;
            left: 5%;
            height: 60%;
            width: 90%;
            box-sizing: border-box;
            border: none;
            border-radius: 30px;
            background-color: #ffffff;
            padding: 20px 10px;
        }
        .form-group {
            display: block;
            padding: 10px;
            margin-bottom: 10px;
            width: 100%;
            position: relative;
            box-sizing: border-box;
        }
        .form-group label {
            display: block;
            color: #60c4c1;
        }
        .form-group input {
            display: inline-block;
            width: 100%;
            padding: 10px;
            font-size: 15px;
            color: #60c4c1;
            border: none;
            border-bottom: 1px solid #60c4c1;
            box-sizing: border-box;
        }
        .submit-btn {
            display: inline-block;
            text-decoration: none;
            width: 90%;
            margin-left: 5%;
            padding: 10px;
            text-align: center;
            background-color: #60c4c1;
            color: #ffffff;
            margin-top: 50px;
            box-sizing: border-box;
            border-radius: 20px;
            border: none;
        }
        .tip {
            position: absolute;
            left: 0;
            bottom: 6%;
            display: block;
            width: 100%;
            text-align: center;
            font-size: 12px;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <h2>共享雨伞</h2>
        <div class="introduction">
            <h4>共享雨伞项目简介</h4>
            <p>共享雨伞是我们与深圳地铁合作推出的便民措施，共享雨伞免押金，免费用，面向市民使用。自2017年8月28日发布以来，市民赞不绝口。我们的理念是普及公益，让公益走进生活。因此，我们邀请你一起为公益出一份力，捐助一把共享雨伞，让更多的市民能够感受到你的爱心。</p>
        </div>
        <button type="button" class="donate-btn">支持公益(20元/把)</button>
        <div class="tip">*会优先抵扣您的奖金</div>
    </section>
    <div class="page-info">
        <form>
            <div class="form-group">
                <label>姓名</label>
                <input type="text" name="name" class="form-group-input" placeholder="请输入您的真实姓名">
            </div>
            <div class="form-group">
                <label>电话</label>
                <input type="text" name="phone" class="form-group-input" placeholder="请输入您的联系电话">
            </div>
            <button type="button" class="submit-btn">提交</button>
        </form>
    </div>
@endsection

@section('javascript')
    <script>
        $('.submit-btn').on('click', function () {
            var formData = $('form').serialize();
            $.ajax({
                url: '{{ route('wechat.sign.donate.info') }}',
                type: 'post',
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        toastr.error(data.message);
                    } else {
                        toastr.success(data.message);
                        if (typeof (data.data.redirect) != 'undefined') {
                            window.location.href = data.data.redirect;
                        }
                    }
                }
            })
        });

        $('.donate-btn').on('click', function () {
            $.ajax({
                url: '{{ route('wechat.sign.donate.order') }}',
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
                        $('.page-info').fadeIn(300);
                    }
                }
            });
        });

        function checkPay(out_trade_no)
        {
            $.ajax({
                url: '{{ route('wechat.sign.donate.check') }}',
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
                        $('.page-info').fadeIn(300);
//                        if (typeof (data.data.redirect) != 'undefined') {
//                            $.timeoutGo(data.data.redirect, 1500);
//                        } else {
//                            $.timeoutReload();
//                        }
                    }
                },
                error: function (error) {
                    alert(error.responseText);
                }
            })
        }
    </script>
@endsection