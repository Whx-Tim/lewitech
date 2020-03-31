@extends('layouts.wechat')

@section('title', '请关注公众号')

@section('css')
    <style>
        section.container {
            width: 100%;
            box-sizing: border-box;
        }

        section.container img {
            width: 100%;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <img src="http://wj.qn.h-hy.com/images/lewitech/badge/subscribe.png">
    </section>
@endsection