@extends('layouts.wechat')

@section('title', '保证卡')

@section('css')
    <style>
        body, html {
            height: 100%;
            width: 100%;
        }

        {{--@font-face {--}}
            {{--font-family: 'jinglei';--}}
            {{--src: url("{{ asset('fonts/fangzhengjinglei.TTF') }}");--}}
        {{--}--}}

        section.container{
            width: 100%;
            height: 100%;
            background-image: url("{{ asset('images/sign/guarantee.png') }}");
            background-size: 100% 100%;
            background-repeat: no-repeat;
            position: relative;
        }

        .title{
            position: absolute;
            top: 6%;
            color: #7ebcbb;
            width: 100%;
            font-size: 28px;
            /*font-family: 'jinglei', sans-serif;*/
            text-align: center;
        }
        .description {
            position: absolute;
            top: 15%;
            /*font-family: 'jinglei', sans-serif;*/
            text-indent: 2em;
            padding: 25px;
            color: #767676;
        }
        .user-list {
            position: absolute;
            top: 43%;
            width: 100%;
            text-align: center;
        }
        .user-list > h4 {
            font-family: 'Microsoft YaHei', sans-serif;
            text-align: center;
            font-weight: bold;
        }

        .help-user-container {
            padding: 10px;
        }
        .help-user-item {
            display: flex;
            flex-direction: row;
            margin-bottom: 10px;
        }
        .help-user {
            text-align: center;
            margin: 0 auto;
            font-size: 11px;
        }
        .help-user > img{
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 50%;
        }
        .my-user {
            position: absolute;
            bottom: 2%;
            width: 100%;
            text-align: center;
        }
        .my-user > h4 {
            text-align: center;
        }
        .my-user > img {
            width: 80px;
            height: 80px;
            border: none;
            border-radius: 50%;
        }

    </style>
@endsection

@section('content')
    <section class="container">
        <div class="title">保证书</div>
        <p class="description">
            为了遇见更加美好的自己，我参加了早起打卡的活动，但不小心漏签了，我的以下好友将一起督促我完成本期签到打卡活动，希望可以领到一张补签卡，让我完成这个目标!
        </p>
        <div class="user-list">
            <h4>以下好友将共同监督我</h4>
            <div class="help-user-container">
                @if(!empty($sign_shares))
                    @foreach($sign_shares->chunk(5) as $chunk)
                        <div class="help-user-item">
                            @foreach($chunk as $sign_share)
                                <div class="help-user">
                                    <img src="{{ $sign_share->user_to->detail->head_img or asset('images/no-avatar.png') }}"><br>{{ $sign_share->user_to->detail->nickname or '' }}
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endif
                {{--<div class="help-user-item">--}}
                    {{--<div class="help-user">--}}
                        {{--<img src="{{ asset('images/no-avatar.png') }}"><br>nickname--}}
                    {{--</div>--}}
                    {{--<div class="help-user">--}}
                        {{--<img src="{{ asset('images/no-avatar.png') }}"><br>nickname--}}
                    {{--</div>--}}
                    {{--<div class="help-user">--}}
                        {{--<img src="{{ asset('images/no-avatar.png') }}"><br>nickname--}}
                    {{--</div>--}}
                    {{--<div class="help-user">--}}
                        {{--<img src="{{ asset('images/no-avatar.png') }}"><br>nickname--}}
                    {{--</div>--}}
                    {{--<div class="help-user">--}}
                        {{--<img src="{{ asset('images/no-avatar.png') }}"><br>nickname--}}
                    {{--</div>--}}
                {{--</div>--}}
            </div>
        </div>
        <div class="my-user">
            <h4>保证人</h4>
            <img src="{{ $my_user->detail->head_img }}"><br>{{ $my_user->detail->nickname }}
        </div>
    </section>
@endsection