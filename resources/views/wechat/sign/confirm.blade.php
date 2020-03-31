@extends('layouts.wechat')

@section('title', '通知')

@section('javascript')
    <script>
        @if($is_apply)
            window.location.href = '{{ route('wechat.sign.index') }}';
        @else
            var confirm = confirm('十二月份早起打卡报名现在开始啦！坚持打卡的老用户可享受九折优惠，新一轮千元等你来拿，事不宜迟，现在就去报名吧~');
            if (confirm) {
                window.location.href = '{{ route('wechat.sign.apply') }}';
            } else {
                window.location.href = '{{ route('wechat.sign.index') }}';
            }
        @endif
    </script>
@endsection