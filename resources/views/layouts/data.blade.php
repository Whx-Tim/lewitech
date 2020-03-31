<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta charset="utf-8">
    <title>共享圈&nbsp;-&nbsp;@yield('title')</title>
    <link href="https://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/plugins/sweetalert/sweetalert.min.css') }}">
    <link href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/materialize/materialize.css') }}" rel="stylesheet">
    <style>
        * {
            padding: 0;
            margin: 0;
        }
    </style>
    @yield('css')
</head>
<body>
@yield('content')
</body>
</html>
<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/materialize/0.100.2/js/materialize.js"></script>
<script src="{{ asset('/plugins/sweetalert/sweetalert.min.js') }}"></script>
<script src="https://cdn.bootcss.com/toastr.js/latest/toastr.min.js"></script>
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
        "timeOut": "3000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "slideDown",
        "hideMethod": "slideUp"
    };
</script>
@yield('javascript')
