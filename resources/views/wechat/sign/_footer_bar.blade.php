@push('head')
<style>
    body {
        padding-bottom: 50px;
    }
    .footer-bar {
        background-color: #ffffff;
        z-index: 99;
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        min-height: 50px;
        display: flex;
        flex-direction: row;
    }
    .footer-bar a {
        flex: 1;
        text-align: center;
        text-decoration: none;
        color: #96d3d1;
    }
</style>
@endpush
<div class="footer-bar">
    <a href="{{ route('wechat.sign.apply') }}"><i class="fa fa-child"></i><br>报名</a>
    <a href="{{ route('wechat.sign.index') }}"><i class="fa fa-flag"></i><br>签到</a>
    <a href="{{ route('wechat.sign.setting') }}"><i class="fa fa-user"></i><br>个人中心</a>
</div>