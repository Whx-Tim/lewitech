@extends('layouts.wechat')

@section('title', '勋章排行榜')

@section('css')
    <style>
        .title {
            width: 100%;
            background-image: url("{{ asset('images/sign/rank/title.png') }}");
            background-size: 100%;
            background-repeat: no-repeat;
            min-height: 60px;
            line-height: 45px;
            text-align: center;
            color: #ffffff;
            font-size: 20px;
        }

        .logo {
            width: 100%;
            text-align: center;
        }
        .logo img {
            width: 60px;
        }

        ul {
            list-style: none;
            width: 100%;
            box-sizing: border-box;
        }

        li {
            display: block;
            /*width: 100%;*/
            background: #cad4d6;
            margin-bottom: 5px;
        }

        li .main {
            padding: 5px 20px;
            position: relative;
        }

        li .main > span.rank {
            font-size: 15px;
            padding: 5px;
            border: none;
            background-color: transparent;
            border-radius: 50%;
            color: #1b9592;
            font-style: italic;
            font-weight: bolder;
            width: 20px;
            height: 20px;
            display: inline-block;
            text-align: center;
            line-height: 20px;
        }

        li img {
            width: 60px;
            height: 60px;
            border: none;
            border-radius: 50%;
            vertical-align: middle;
            margin-left: 20px;
        }

        li .main > span.nickname {
            color: #353535;
            font-size: 14px;
            margin-left: 30px;
        }

        li .main > span.more {
            display: inline-block;
            float: right;
            margin-top: 25px;
            width: 30px;
            height: 20px;
            background-image: url("{{ asset('images/sign/rank/more.png') }}");
            background-size: 100%;
            background-repeat: no-repeat;
            vertical-align: middle;
        }

        li:nth-child(2) .main > span.rank,
        li:nth-child(3) .main > span.rank,
        li:nth-child(4) .main > span.rank{
            background-color: #35b3b0;
            color: #ffffff;
            position: relative;
        }
        li:nth-child(2) .main > span.rank:after,
        li:nth-child(3) .main > span.rank:after,
        li:nth-child(4) .main > span.rank:after{
            content: '';
            position: absolute;
            display: inline-block;
            top: -7px;
            left: 12px;
            width: 15px;
            height: 15px;
            background-size: 100%;
            background-repeat: no-repeat;
        }

        li:nth-child(2) .main > span.rank:after {
            background-image: url("{{ asset('images/sign/rank/first.png') }}");
        }
        li:nth-child(3) .main > span.rank:after {
            background-image: url("{{ asset('images/sign/rank/second.png') }}");
        }
        li:nth-child(4) .main > span.rank:after {
            background-image: url("{{ asset('images/sign/rank/third.png') }}");
        }

        .more-content {
            display: none;
            width: 100%;
            padding: 0 10%;
            background-image: url("{{ asset('images/sign/rank/more_bg.png') }}");
            background-size: 100%;
            background-repeat: no-repeat;
            min-height: 80px;
            box-sizing: border-box;
            background-color: #ffffff;
            text-align: center;
        }
        .more-content div {
            display: inline-block;
            color: #ffffff;
            margin-top: 10%;
        }
        .more-content div img {
            width: 40px;
            height: 40px;
            margin-left: 10px;
            vertical-align: text-bottom;
        }

        ul li.self  {
            background-image: url("{{ asset('images/sign/rank/self_bg.png') }}");
            background-size: 100%;
            background-repeat: no-repeat;
        }
        ul li.self > .main > span.rank {
            color: #353535;
        }
        ul li.self > .main > span.more {
            background-image: url("{{ asset('images/sign/rank/dropdown.png') }}");
            width: 20px;
            height: 15px;
            margin-right: 5px;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <div class="title">早起勋章排行榜</div>
        <div class="logo">
            <img src="{{ asset('images/logo_green.png') }}">
        </div>
        <ul>
            <li class="self">
                <div class="main">
                    <span class="rank">{{ $rank==0 ? '无' : $rank }}</span>
                    <img src="{{ $user->detail->head_img }}" alt="">
                    <span class="nickname">{{ $user->detail->nickname }}</span>
                    <span class="more"></span>
                </div>
                <div class="more-content">
                    <div class="gold">
                        <img src="{{ asset('images/sign/rank/gold.png') }}" >
                        x{{ $user->signMedal->gold or 0 }}
                    </div>
                    <div class="silver">
                        <img src="{{ asset('images/sign/rank/silver.png') }}" >
                        x{{ $user->signMedal->silver or 0 }}
                    </div>
                    <div class="bronze">
                        <img src="{{ asset('images/sign/rank/bronze.png') }}" >
                        x{{ $user->signMedal->bronze or 0 }}
                    </div>
                </div>
            </li>
            @foreach($medals as $key => $medal)
                <li>
                    <div class="main">
                        <span class="rank">{{ ($key+1) }}</span>
                        <img src="{{ $medal->user->detail->head_img }}" alt="">
                        <span class="nickname">{{ $medal->user->detail->nickname }}</span>
                        <span class="more"></span>
                    </div>
                    <div class="more-content">
                        <div class="gold">
                            <img src="{{ asset('images/sign/rank/gold.png') }}" >
                            x{{ $medal->gold or 0 }}
                        </div>
                        <div class="silver">
                            <img src="{{ asset('images/sign/rank/silver.png') }}" >
                            x{{ $medal->silver or 0 }}
                        </div>
                        <div class="bronze">
                            <img src="{{ asset('images/sign/rank/bronze.png') }}" >
                            x{{ $medal->bronze or 0 }}
                        </div>
                    </div>
                </li>
            @endforeach

            {{--<li>--}}
                {{--<div class="main">--}}
                    {{--<span class="rank">1</span>--}}
                    {{--<img src="{{ asset('images/sign/test-avatar.png') }}" alt="">--}}
                    {{--<span class="nickname">XIN</span>--}}
                    {{--<span class="more"></span>--}}
                {{--</div>--}}
                {{--<div class="more-content">--}}
                    {{--<div class="gold">--}}
                        {{--<img src="{{ asset('images/sign/rank/gold.png') }}" >--}}
                        {{--x0--}}
                    {{--</div>--}}
                    {{--<div class="silver">--}}
                        {{--<img src="{{ asset('images/sign/rank/silver.png') }}" >--}}
                        {{--x0--}}
                    {{--</div>--}}
                    {{--<div class="bronze">--}}
                        {{--<img src="{{ asset('images/sign/rank/bronze.png') }}" >--}}
                        {{--x0--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</li><li>--}}
                {{--<div class="main">--}}
                    {{--<span class="rank">1</span>--}}
                    {{--<img src="{{ asset('images/sign/test-avatar.png') }}" alt="">--}}
                    {{--<span class="nickname">XIN</span>--}}
                    {{--<span class="more"></span>--}}
                {{--</div>--}}
                {{--<div class="more-content">--}}
                    {{--<div class="gold">--}}
                        {{--<img src="{{ asset('images/sign/rank/gold.png') }}" >--}}
                        {{--x0--}}
                    {{--</div>--}}
                    {{--<div class="silver">--}}
                        {{--<img src="{{ asset('images/sign/rank/silver.png') }}" >--}}
                        {{--x0--}}
                    {{--</div>--}}
                    {{--<div class="bronze">--}}
                        {{--<img src="{{ asset('images/sign/rank/bronze.png') }}" >--}}
                        {{--x0--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</li>--}}
        </ul>
    </section>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            $('span.more').each(function () {
                var $this = $(this);
                var switch_more = true;
                $this.on('touchstart', function () {
                    if (switch_more) {
                        $this.parent().next().slideDown('fast');
                        switch_more = false;
                    }  else {
                        $this.parent().next().slideUp('fast');
                        switch_more = true;
                    }
                })
            })
        })
    </script>
@endsection