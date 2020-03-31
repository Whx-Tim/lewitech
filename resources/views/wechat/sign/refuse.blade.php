@extends('layouts.temp')

@section('title', '再次确认')

@push('css')
<style>
    body, html {
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,.6);
    }
    .alert {
        position: absolute;
        top: 20%;
        left: 10%;
        width: 80%;
        height: 30%;
        box-sizing: border-box;
        background-color: #ffffff;
    }
    .content {
        text-align: center;
        padding: 60px 0;
    }
    .button-area {
        position: absolute;
        width: 100%;
        bottom: 0;
        left: 0;
        display: flex;
        flex-direction: row;
    }
    .button-item {
        flex: 1;
        display: inline-block;
        text-decoration: none;
        background-color: #60c4c1;
        color: #ffffff;
        padding: 20px;
        text-align: center;
        box-sizing: border-box;
    }
</style>
@endpush

@section('content')
    <div class="alert">
        <div class="content">
            请施主三思 T^T
        </div>
        <div class="button-area">
            <a href="{{ route('wechat.sign.apply') }}" class="button-item" style="border-right: 1px solid #ffffff">我再看看</a>
            <a href="javascript:notice();" class="button-item">去意已决</a>
        </div>
    </div>
@endsection

@push('javascript')
<script src="{{asset('/js/plugins/jquery/jquery-2.2.3.min.js')}}"></script>
<script>
    function notice()
    {
        $.ajax({
            url: '{{ route('wechat.sign.refuse.real') }}',
            type: 'get',
            dataType: 'json',
            success: function (data) {
                alert('欢迎随时给我们提宝贵意见！（注：后续您将不会收到早起打卡报名消息提醒，但如果您想继续参与打卡活动的话，可点击菜单栏“早起打卡”按钮进入报名页面。');
                window.location.href = '{{ route('wechat.sign.setting') }}'
            },
            error: function () {
                alert('欢迎随时给我们提宝贵意见！（注：后续您将不会收到早起打卡报名消息提醒，但如果您想继续参与打卡活动的话，可点击菜单栏“早起打卡”按钮进入报名页面。');
                window.location.href = '{{ route('wechat.sign.setting') }}'
            }
        });
    }
</script>
@endpush