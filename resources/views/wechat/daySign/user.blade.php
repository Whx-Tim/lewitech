@extends('layouts.wechat')

@section('css')
    <style>
        body {
            padding: 10px;
            padding-bottom: 0;
            background-color: #828284;
        }

        section.container {
            background-color: white;
            border-radius: 5px;
            border: 1px solid black;
            text-align: center;
            position: relative;
            padding: 10px;
            height: 90vh;
        }

        .user-area {
            text-align: center;
        }
        .user-area > img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
        .user-area > p {
            text-align: center;
            margin-top: 5px;
            font-size: 15px;
            font-weight: bolder;
        }

        .info-area {
            margin-top: 10px;
            display: flex;
            flex-direction: row;
        }
        .info-area > .info-item {
            flex: 1;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .info-item > .img-box {
            flex: 1;
            text-align: center;
        }
        .img-box > img {
            height: 30px;
        }
        .info-item > p {
            flex: 1;
        }
        .info-item > span {
            flex: 1;
            color: #cbc9c9;
        }

        .record-list {
            list-style: none;
            margin-top: 20px;
            border-top: 1px solid #cbc9c9;
            max-height: 45vh;
            overflow-y: auto;
        }
        .record-list > li {
            padding: 10px 0;
            position: relative;
            border-bottom: 1px solid #cbc9c9;
            font-size: 13px;
            text-align: left;
        }
        .record-list > li:before {
            content: '';
            height: 5px;
            width: 5px;
            border: 2px solid black;
            border-radius: 50%;
            position: absolute;
            top: 15px;
            left: 20px;
        }
        .record-list > li > div {
            display: inline-block;
        }
        .record-list > li > .date {
            margin-left: 40px;
        }
        .record-list > li > .time {
            float: right;
            margin-right: 20px;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <div class="user-area">
            <img src="{{ $user->detail->head_img }}" alt="">
            <p>{{ $user->detail->nickname }}</p>
        </div>
        <div class="info-area">
            <div class="info-item">
                <div class="img-box">
                    <img src="{{ asset('images/day_sign/user_record.png') }}" alt="">
                </div>
                <p>打卡记录</p>
                <span>{{ count($signs) or 0 }} 条</span>
            </div>
            <div class="info-item">
                <div class="img-box">
                    <img src="{{ asset('images/day_sign/user_reward.png') }}" alt="">
                </div>
                <p>累计奖金</p>
                <span>{{ $reward or 0 }} 元</span>
            </div>
            <div class="info-item">
                <div class="img-box">
                    <img src="{{ asset('images/day_sign/user_value.png') }}" alt="">
                </div>
                <p>早起值</p>
                <span>{{ $value or 0 }}%</span>
            </div>
        </div>
        <ul class="record-list">
            @foreach($signs as $sign)
                <li>
                    <div class="date">{{ \Carbon\Carbon::parse($sign->time)->format('Y-m-d') }}</div>
                    <div class="time">{{ \Carbon\Carbon::parse($sign->time)->format('H:i') }} 打卡</div>
                </li>
            @endforeach
        </ul>
    </section>
@endsection