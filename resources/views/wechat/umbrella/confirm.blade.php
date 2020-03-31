@extends('wechat.umbrella.common.layout')

@section('title', '确认借伞')

@section('css')
    <style>
        html, body {
            width: 100%;
            height: 100%;
        }

        .container {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .container img {
            width: 60%;
            margin-bottom: 30px;
        }

        .container h4 {
            text-align: center;
        }
        .container h5 {
            text-align: center;
        }

        .container button {
            border-radius: 20px;
            width: 80%;
            margin-top: 20px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <img src="{{ asset('images/umbrella/result_good.png') }}">
        {{--<h4>{{ $message }}</h4>--}}
        <button type="button" class="general-btn" id="redirect-btn">确定</button>
        {{--<h5>3秒后将自动跳转...</h5>--}}
    </section>
@endsection

@section('javascript')
    <script>

    </script>
@endsection