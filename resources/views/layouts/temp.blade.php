<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
    <meta name="format-detection" content="telephone=no,email=no,address=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-title" content="共享圈&nbsp;-&nbsp;@yield('title')">
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <style>
        * {
            padding: 0;
            margin: 0;
        }
    </style>
    @yield('css')
    @stack('css')
</head>
<body>
@yield('content')
</body>
</html>
@stack('javascript')
@yield('javascript')
