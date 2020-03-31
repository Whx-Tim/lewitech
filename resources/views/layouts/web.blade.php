<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="乐微科技">
    <meta name="keywords" content="乐微">
    <meta name="keywords" content="深圳公益爱心伞">
    <meta name="keywords" content="公益爱心伞">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', '乐微科技') }}&nbsp;-&nbsp;@yield('title')</title>

    <!-- Styles -->
    <link href="{{ asset('css/materialize/materialize.css') }}" rel="stylesheet">
    <link href="{{ asset('css/materialize/material-icons.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('lewitech.ico') }}">
    <style>
        html {
            font-family: Microsoft YaHei, sans-serif;

        }

        .online-btn {
            background-color: #6284d6;
            color: #ffffff;
            padding: 10px;
            width: 100%;
            border: none;
            border-radius: 25px;
        }
    </style>
    @stack('css')
    <!-- Scripts -->
</head>
<body>
<div id="app">
    <nav class="white">
        <div class="container">
            <div class="nav-wrapper">
                <a href="{{ route('web.index') }}" class="brad-logo"><img src="{{ asset('images/logo_green.png') }}" width="100" style="margin-top: 10px"></a>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <li><a href="{{ route('web.index') }}" class="grey-text darken-4">首页</a></li>
                    <li><a href="{{ route('web.introduction') }}" class="grey-text darken-4">公司介绍</a></li>
                    <li><a href="{{ route('web.app') }}" class="grey-text darken-4">项目介绍</a></li>
                </ul>
            </div>
        </div>
    </nav>
    @yield('content')
    <footer class="page-footer grey darken-3" style="padding: 60px 0;">
        <div class="container">
            <div class="row">
                <div class="col l3 offset-l3" style="border-right: 1px solid #ffffff !important;">
                    <div class="col l10">
                        <h4 class="white-text"><img src="{{ asset('images/logo_transparent.png') }}" width="40" style="vertical-align: sub;margin-right: 5px">乐微科技</h4>
                        <p style="text-indent: 2em">欢迎了解我们，乐微科技（深圳）有限公司将竭诚打造一流的互联网高校校友产品</p>
                        <p>共建&nbsp;-&nbsp;共享&nbsp;-&nbsp;互助</p>
                    </div>
                </div>
                <div class="col l6">
                    <div class="col l16 offset-l1">
                        <p style="margin-bottom: 0">联系热线:</p>
                        <h5 style="margin-top: 0 !important;">0755-26533151</h5>
                        <button type="button" class="online-btn">在线咨询</button>
                        <p><i class="material-icons" style="vertical-align: bottom">email</i>&nbsp;HKH@lewitech.cn</p>
                    </div>
                    <div class="col l4 offset-l1 center-align">
                        <img src="{{ asset('images/subscribe.png') }}" width="100" class="white" style="margin-top: 40px"><br>
                        校友共享圈公众号
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-copyright">
            <div class="container">
                Copyright&nbsp;&copy;&nbsp;2017&nbsp;乐微科技（深圳）有限公司. All Rights Reserved
            </div>
        </div>
    </footer>
</div>
<script src="{{asset('/js/plugins/jquery/jquery-2.2.3.min.js')}}"></script>
<script src="https://cdn.bootcss.com/materialize/0.100.2/js/materialize.min.js"></script>
@stack('javascript')
</body>
</html>
