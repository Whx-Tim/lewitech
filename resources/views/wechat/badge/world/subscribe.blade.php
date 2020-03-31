@extends('layouts.wechat')

@section('title', '扫码获取您的高清头像')

@section('css')
    <style>
        section.container {
            width: 100%;
            box-sizing: border-box;
        }

        section.container img {
            width: 100%;
        }

        h4 {
            text-align: center;
            margin-top: 20px;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <h4>扫码以便获取您的高清头像</h4>
        <img src="http://wj.qn.h-hy.com/images/lewitech/badge/world/subscribe.jpeg">
    </section>
@endsection