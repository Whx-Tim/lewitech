@extends('layouts.wechat')

@section('title', '请关注公众号')

@section('css')
    <style>
        section.container {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            overflow: hidden;
        }

        section.container h4 {
            text-align: center;
        }

        section.container img {
            width: 80%;
            margin-left: 10%;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <h4>请关注我们公众号才能使用相应的功能</h4>
        <h4>点击功能菜单早起打卡参与活动</h4>
        <img src="{{ asset('images/subscribe.png') }}">
    </section>
@endsection