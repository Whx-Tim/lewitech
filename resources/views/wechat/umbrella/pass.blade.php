@extends('layouts.wechat')

@section('title', '借伞凭证')

@section('css')
    <style>
        html,
        body {
            height: 100%;
            width: 100%;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url("/images/umbrella/pass-background.png");
            background-size: 100% 100%;
            background-repeat: no-repeat;
        }

        section.container {
            width: 80%;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        section.container h1 {
            font-size: 30px;
            color: #ffffff;
            margin-bottom: 30px;
        }

        .box-logo {
            width: 90%;
            margin-left: 5%;
            height: 250px;
            background-color: transparent;
            border: none;
            border-radius: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .box-logo img {
            width: 90%;
        }

        .instruction {
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .instruction h3 {
            text-align: center;
            font-weight: bold;
        }

        .instruction h4 {
            text-align: center;
            font-weight: 200;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .instruction p {
            display: inline-block;
            padding: 5px;
            font-size: 18px;
            border: 1px solid #ffffff;
        }

        .general-btn {
            display: block;
            margin-top: 20px;
            background-color: #48acc8;
            color: #fff;
            padding: 15px;
            border: none;
            width: 100%;
            letter-spacing: .4em;
            font-size: 15px;
            font-weight: 400;
            margin-bottom: 30px;
        }

    </style>
@endsection

@section('content')
    <section class="container">
        <h1>借伞成功</h1>
        <div class="box-logo"><img src="{{ asset('/images/umbrella/pass-logo.png') }}"></div>
        <div class="instruction">
            <h3>使用说明</h3>
            <h4>请向工作人员出示该凭证，领取共享雨伞</h4>
            <p><span>借伞时间:</span>{{ $time }}</p>
            <button type="button" class="general-btn" id="return-btn">返回</button>
        </div>
    </section>
    @include('wechat.umbrella.common.footer')
@endsection

@section('javascript')
    {{--<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>--}}
    {{--<script type="text/javascript" charset="utf-8">--}}

    {{--</script>--}}
    <script>
        alert('您每次使用的公益爱心伞，都源于社会公益人士的捐赠，让我们一起感谢这份善心，祝您用伞愉快');
        $('#return-btn').click(function () {
            window.location.href = '{{ route('wechat.umbrella.index') }}';
        });
    </script>
@endsection