@extends('layouts.temp')

@section('title', '报名分享')

@section('content')
    <img src="{{ $path }}" style="width: 100%">
@endsection

@section('javascript')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        alert('可以保存图片，邀请更多人来参与瓜分奖金~');

        wx.config(<?php echo $js->config(array('hideAllNonBaseMenuItem'), false) ?>);
        wx.ready(function(){
            wx.hideAllNonBaseMenuItem();
        });
    </script>
@endsection