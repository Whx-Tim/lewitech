@extends('layouts.wechat')

@section('title', '乐微互助')

@section('css')
    <style>
        html, body {
            background-color: #e3ebec;
        }
        section.container {

        }
        form {
            background-color: #ffffff;
            padding: 10px 20px 40px 20px;
        }
        h4 {
            font-size: 20px;
            font-weight: bolder;
            text-align: center;
            border-bottom: 1px solid rgba(0,0,0, .2);
            padding: 10px;
        }
        .input-group {
            border-bottom: 1px solid rgba(0, 0, 0, .2);
            padding: 10px;
        }
        .input-group > label {
            display: inline-block;
            width: 20vw;
            font-size: 14px;
        }
        .input-control {
            width: 50vw;
            margin-left: 10vw;
            text-align: right;
            border: none;
            font-size: 14px;
        }
        .footer-btn-area {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100vw;
            min-height: 10vh;
            box-sizing: border-box;
            padding: 20px 40px;
            background-color: #e3ebec;
        }
        .btn-apply {
            width: 100%;
            background-color: #3dc9c0;
            color: #ffffff;
            font-weight: bolder;
            text-align: center;
            padding: 10px 0;
            border: none;
            border-radius: 20px;
            outline: none;
            font-size: 14px;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <form>
            <h4>填写个人信息</h4>
            <div class="input-group">
                <label>姓名</label>
                <input type="text" class="input-control" name="name" placeholder="请填写会员真实姓名" value="{{ empty($help->name) ? '' : $help->name }}">
            </div>
            <div class="input-group">
                <label>身份证</label>
                <input type="text" class="input-control" name="id_number" placeholder="请填写会员真实身份证号" value="{{ empty($help->id_number) ? '' : $help->id_number }}">
            </div>
            <div class="input-group">
                <label>互助类型</label>
                <input type="text" class="input-control" name="type" onfocus="blur();" placeholder="重病互助" value="重病互助" disabled>
            </div>
            <div class="input-group">
                <label>报名类型</label>
                <input type="text" class="input-control" name="apply_type" onfocus="blur();" placeholder="{{ $is_shareholder ? '乐微股东' : '乐微互助发起人' }}" value="{{ $is_shareholder ? '乐微股东' : '乐微互助发起人' }}" disabled>
            </div>
        </form>
        <div class="footer-btn-area">
            <button type="button" class="btn-apply">立即加入</button>
        </div>
    </section>
@endsection

@section('javascript')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        $('.btn-apply').on('click', function () {
            var formData = $('form').serialize();
            $.ajax({
                url: '{{ route('wechat.help.apply') }}',
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
                url: '{{ route('wechat.help.pay.check') }}',
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
                        $.timeoutGo('{{ route('wechat.help.index') }}');
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