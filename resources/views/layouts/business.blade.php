<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/business/base.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/business/single.css') }}">
    @yield('css')
</head>
<body>
@yield('content')
@yield('javascript')
</body>
</html>
