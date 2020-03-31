@extends('layouts.wechat')

@section('title', '奖金排行榜')

@section('css')
    <style>
        .sign-list {
            border: 1px solid #80bcba;
            box-sizing: border-box;
            width: 94%;
            margin-left: 3%;
            margin-top: 3%;
        }
        .sign-list h4 {
            text-align: center;
            font-size: 25px;
            color: #ffffff;
            padding: 20px 0;
            background-color: #80bcba;
        }
        .sign-list .select-area {
            display: flex;
            flex-direction: row;
            border-bottom: 1px solid #80bcba;
            padding: 10px 0;
        }
        .sign-list .select-area .select-item {
            flex: 1;
            color: #c7c7c7;
            font-size: 20px;
            text-align: center;
        }
        .sign-list .select-area .select-item.active {
            color: #757575;
        }
        .sign-list ul {
            padding: 5px;
        }
        .sign-list ul li {
            padding: 5px 0;
            border-bottom: 1px solid rgb(240,240,240);
            display: flex;
            flex-direction: row;
        }
        .sign-list ul li.active {
            color: #94d3d2;
        }
        .sign-list ul li.active div.number {
            color: #94d3d2 !important;
        }
        .sign-list ul li div {
            flex: 1;
            text-align: center;
            line-height: 40px;
        }
        .sign-list ul li div.nickname {
            flex: 4;
        }
        .sign-list ul li div.duration {
            flex: 2;
        }

        .sign-list ul li div.number {
            font-style: italic;
            color: #f69045;
        }
        .sign-list .user-avatar {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 100%;
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <div class="sign-list">
            <h4>奖金排行榜</h4>
            <ul>
                <li>
                    <div class="number">排名</div>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="nickname">昵称</div>
                    <div class="duration">本次奖金</div>
                </li>
                {{--<li class="active">--}}
                    {{--<div class="number">{{ $user_rank }}</div>--}}
                    {{--<img src="{{ $user_sign->user->detail->head_img }}" class="user-avatar">--}}
                    {{--<div class="nickname">{{ str_limit($user_sign->user->detail->nickname, 10) }}</div>--}}
                    {{--<div class="duration">{{ $user_sign->total_reawrd or 0 }}</div>--}}
                {{--</li>--}}
                @foreach($reward_ranks as $key => $item)
                    @if($key < 10)
                    <li>
                        <div class="number">{{ (int)($key+1) }}</div>
                        <img src="{{ $item->user->detail->head_img }}" class="user-avatar">
                        <div class="nickname">{{ str_limit($item->user->detail->nickname, 10) }}</div>
                        <div class="duration">{{ $item->now_reward }}</div>
                    </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </section>
@endsection