@extends('layouts.wechat')

@section('title', '签到周报')

@section('content')
    <section class="container">
        <img src="{{ $path }}" style="width: 100%">
    </section>
@endsection

@push('javascript')
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
<script>

    @if($exist)
        alert('长按图片可以保存喔~');
    @else
        alert('您没有获得本周的战报~请下周保持签到即可获得');
    @endif

    wx.config(<?php echo $js->config(array('hideAllNonBaseMenuItem'), false) ?>);
    wx.ready(function(){
        wx.hideAllNonBaseMenuItem();
    });
</script>
@endpus