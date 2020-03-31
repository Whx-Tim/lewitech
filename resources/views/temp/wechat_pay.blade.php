@extends('layouts.wechat')

@section('title', '每日流水')

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
            <table>
                <thead>
                    <th>状态</th>
                    <th>金额</th>
                    <th>时间</th>
                    <th>操作</th>
                </thead>
                <tbody>
                @foreach($deals as $deal)
                    <tr>
                        <td>{{ $deal->result_code or '' }}</td>
                        <td>{{ $deal->total_fee or '' }}</td>
                        <td>{{ $deal->created_at or '' }}</td>
                        <td>
                            @if($deal->result_code == 'PAID')
                                <button type="button" class="refund-btn" refund-id="{{ $deal->id or 0 }}">退款</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="footer">
            <button type="button" class="normal-btn">每日流水</button>
        </div>
    </section>
@endsection

@section('javascript')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        $('.refund-btn').each(function () {
            var $this = $(this);
            $(this).on('click', function () {
                alert($this.attr('refund-id'));
                var id = $this.attr('refund-id');
                alert(id);
                $.ajax({
                    url: '{{ url('wechat/pay/refund') }}'+'/'+id,
                    type: 'get',
                    dataType: 'json',
                    success: function (data) {
                        if (data.code) {
                            toastr.error(data.message);
                        } else {
                            toastr.success(data.message);
                            $.timeoutReload();
                        }
                    }
                })
            })
        });

        $('.normal-btn').on('click', function () {
            $.ajax({
                url: '{{ route('wechat.pay.everyDay') }}',
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
                url: '{{ route('wechat.pay.response') }}',
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