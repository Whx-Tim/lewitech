@extends('layouts.wechat')

@section('title', '签到状态')

@section('css')
    <style>
        html,
        body {
            width: 100%;
            height: 100%;
        }

        section.container {
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            position: relative;
        }

        .container img {
            width: 100%;
            height: 100%;
        }

        .container span {
            position: absolute;
            left: 32%;
            top: 36%;
            font-size: 18px;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <img src="{{ $awardUrl }}" alt="">
{{--        <img src="{{ asset('images/getup/first.jpeg') }}" alt="">--}}
{{--        <span>{{ $user['nickname'] }}</span>--}}
        @if(!$is_normal)
            <span>{{ $user['nickname'] }}</span>
        @endif
    </section>
@endsection

@section('javascript')

@endsection