<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
    <meta name="format-detection" content="telephone=no,email=no,address=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-title" content="共享圈&nbsp;-&nbsp;@yield('title')">
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>校友圈&nbsp;-&nbsp;@yield('title')</title>
    <link href="https://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/plugins/weui/weui.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/sweetalert/sweetalert.min.css') }}">
    <style>
        * {
            padding: 0;
            margin: 0;
        }
    </style>
    @yield('css')
    @stack('head')
</head>
<body>
@yield('content')
</body>
</html>
<script src="{{asset('/js/plugins/jquery/jquery-2.2.3.min.js')}}"></script>
<script src="{{ asset('/plugins/sweetalert/sweetalert.min.js') }}"></script>
<script src="https://cdn.bootcss.com/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('/plugins/weui/weui.js') }}"></script>
<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-full-width",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "1500",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "slideDown",
        "hideMethod": "slideUp"
    };

    $.__proto__.timeoutGo = function (redirect, delay) {
        var default_delay = arguments[1] ? arguments[1] : 1000;
        setTimeout(function () {
            window.location.href = redirect
        }, default_delay);
    };

    $.__proto__.timeoutReload = function (delay, refresh_cache) {
        var default_delay = arguments[0] ? arguments[0] : 1000;
        var default_refresh = arguments[1] ? arguments[1] : false;
        setTimeout(function () {
            window.location.reload(default_refresh)
        }, default_delay);
    }
</script>
@stack('javascript')
@yield('javascript')
