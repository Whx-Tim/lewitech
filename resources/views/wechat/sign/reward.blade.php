@extends('layouts.wechat')

@section('title', '我的奖金')

@section('css')
    <style>
        body, html {
            height: 100%;
            width: 100%;
        }

        section.container {
            width: 100%;
            height: 100%;
            background-image: url("{{ asset('images/sign/reward-background.png') }}");
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }

        .wallet {
            position: relative;
            width: 94%;
            margin-left: 3%;
            background-image: url("{{ asset('images/sign/wallet.png') }}");
            background-size: 100%;
            background-repeat: no-repeat;
            min-height: 200px;
            margin-top: 50px;
        }

        .wallet h4 {
            padding-left: 20px;
            position: absolute;
            font-size: 25px;
            left: 0;
            bottom: 70px;
            color: #ffffff;
        }
        .wallet p {
            padding-left: 20px;
            position: absolute;
            left: 0;
            bottom: 40px;
            color: #ffffff;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            width: 90%;
            margin-left: 5%;
            padding: 10px;
            text-align: center;
            background-color: #60c4c1;
            color: #ffffff;
            margin-top: 40px;
            box-sizing: border-box;
            border-radius: 20px;
        }

        .logo {
            text-align: center;
            position: absolute;
            left: 0;
            bottom: 10px;
            display: block;
            width: 100%;
        }
        .logo > img {
            width: 70px;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <div class="wallet">
            <h4>钱包</h4>
            <p>您获得 {{ $reward }} 元</p>
        </div>
        <a href="{{ route('wechat.sign.donate') }}" class="donate-btn btn">捐赠</a>
        <a href="javascript:;" class="withdraw-btn btn">提现</a>
    </section>
    <div class="logo">
        <img src="{{ asset('images/logo_green.png') }}">
    </div>
@endsection

@section('javascript')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        $('.withdraw-btn').on('click', function () {
            $.ajax({
                url: '{{ route('wechat.sign.reward') }}',
                type: 'post',
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
        });

        wx.config(<?php echo $js->config(array('hideAllNonBaseMenuItem'), false) ?>);
        wx.ready(function(){
            wx.hideAllNonBaseMenuItem();
        });
    </script>
@endsection