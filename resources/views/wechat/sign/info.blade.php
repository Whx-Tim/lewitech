@extends('layouts.wechat')

@section('title', '签到信息')

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
            box-sizing: border-box;
            font-size: 15px;
        }

        .top {
            margin-top: 50px;
        }
        .top .heading {
            text-align: center;
            color: #e1e1e1;
        }
        .top h5 {
            text-align: center;
            color: #3e3d3d;
        }

        .body .heading {
            text-align: center;
            margin-right: 5%;
            margin-left: 5%;
            border-bottom: 1px solid black;
            padding-top: 10px;
            padding-bottom: 15px;
            color: #3e3d3d;
        }

        .body p {
            position: relative;
            padding: 0 30px;
            padding-top: 60px;
        }
        .body p:before {
            content: "“";
            font-weight: bold;
            font-size: 60px;
            position: absolute;
            top: 10px;
            left: -5px;
            color: #e5e5e5;
        }

        .body p b {
            display: block;
        }
        .body p span {
            display: block;
            color: #ababab;
        }

        .normal-btn {
            margin-top: 10px;
            width: 100%;
            background-color: #3ed2c2;
            color: #ffffff;
            padding: 10px;
            font-size: 15px;
            border-radius: 25px;
            box-sizing: border-box;
            border: none;
        }

        .card {
            width: 100%;
            min-height: 200px;
            background-color: #5e748f;
            box-sizing: border-box;
            position: relative;
        }

        .card-box {
            position: absolute;
            top: 40px;
            left: 5%;
            width: 90%;
            min-height: 200px;
            border: none;
            background-image: url("/images/sign/apply-banner.png");
            background-size: 100% 100%;
            background-repeat: no-repeat;
            border-radius: 30px;
            color: #ffffff;
        }

        .box-top {
            padding-top: 20px;
        }
        .user-avatar {
            display: inline-block;
            padding-left: 10px;
        }
        .user-avatar img {
            height: 80px;
            width: 80px;
            border: none;
            border-radius: 50%;
        }
        .user-info {
            display: inline-block;
            width: calc(100% - 100px);
        }
        .user-info .title {
            text-align: right;
        }
        .user-info .name {
            margin-top: 30px;
            text-align: right;
        }

        .box-bottom {
            margin-top: 10px;

        }
        .box-bottom .time,
        .box-bottom .description {
            padding-right: 10px;
            text-align: right;
        }

        .box-bottom .description b{
            font-size: 35px;
            vertical-align: middle;
        }

        .box-bottom .description b:last-child {
            line-height: 20px;
        }




    </style>
@endsection

@section('content')
    <section class="container">
        <div class="card">
            <div class="card-box">
                <div class="box-top">
                    <div class="user-avatar">
                        <img src="{{ asset('/images/no-avatar.png') }}">
                    </div>
                    <div class="user-info">
                        <h2 class="title">早起打卡</h2>
                        <h4 class="name">王先生</h4>
                    </div>
                </div>
                <div class="box-bottom">
                    <div class="time">
                        XX时间：2017.7.31 - 2017.8.25
                    </div>
                    <div class="description">
                        <b>“</b><span>乐微早起，让奋斗不再孤单！</span><b>”</b>
                    </div>
                </div>
            </div>
        </div>
        <div class="top">
            <h5>-计划介绍-</h5>
            <h4 class="heading">PLAN INTRODUCITION</h4>
        </div>
        <div class="body">
            <h3 class="heading">早起打卡&nbsp;-&nbsp;赏金打卡</h3>
            <p>
                <b>你离成功只差2个字——早起</b>
                <span>参与活动需上缴押金30元，平均一天一块钱，督促自己每日早起打卡，坚持完成所有签到即返回押金，漏签可有机会获得补签机会，连续漏签2次则押金全部扣除。扣除的押金将进入奖金池，坚持完成一个月早起打卡的用户，将瓜分奖金池内的奖金。</span>
            </p>
        </div>
    </section>
    @include('wechat.sign._footer_bar')
@endsection