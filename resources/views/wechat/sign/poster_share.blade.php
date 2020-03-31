@extends('layouts.wechat')

@section('title', '补签海报')

@section('content')
    <section class="container">
        <img src="{{ $path }}" style="width: 100%">
    </section>
@endsection

@section('javascript')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        alert('请长按保存图片，分享图片给好友进行补签哦！');

        wx.config(<?php echo $js->config(array('hideAllNonBaseMenuItem'), false) ?>);
        wx.ready(function(){
            wx.hideAllNonBaseMenuItem();
        });
    </script>
@endsection