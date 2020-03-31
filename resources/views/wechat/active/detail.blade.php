@extends('layouts.wechat')

@section('title', '活动详情')

@section('css')
    <link rel="stylesheet" href="{{ url('css/active/detail.css') }}">
@endsection

@section('content')
    <section class="container">
        <div class="banner">
            <img src="http://wj.qn.h-hy.com/{{ $active->poster }}" alt="">
        </div>
        <div class="nav">
            <span class="left-nav active">活动</span>
            <span class="right-nav">详情</span>
        </div>
        <div id="page-info">
            <h4>{{ $active->name }}</h4>
            <p><span class="view-count">{{ $active->view->count }}</span><span class="apply-count">{{ $active->enrolls()->count() }}</span></p>
            <ul>
                <li><span>发起方</span>{{ $active->sponsor }}</li>
                <li><span>活动开始时间</span><span class="time">{{ $active->start_time }}</span></li>
                <li><span>活动结束时间</span><span class="time">{{ $active->end_time }}</span></li>
                <li><span>报名截止时间</span><span class="time">{{ $active->end_at }}</span></li>
                <li><span>活动地址</span>{{ $active->location }}</li>
                <li><span>咨询电话</span>{{ $active->phone }}</li>
                <li><span>限制人数</span>{{ $active->persons == 0 ? '无' : $active->persons }}</li>
            </ul>
        </div>
        <div id="page-detail">
            <h4>{{ $active->name }}</h4>
            <p>{{ $active->description }}</p>
        </div>
        <div class="info-footer">
            <button type="button" class="consult-btn">返回主页</button>
            {{--<button type="button" class="consult-btn">咨询主办方</button>--}}
            @if($is_apply)
                <button type="button" class="disable">取消报名</button>
            @else
                @if($is_overdue)
                    <button type="button" class="disable" disabled>报名已截止</button>
                @else
                    <button type="button" class="apply-btn">参与活动</button>
                @endif
            @endif
        </div>
    </section>
@endsection

@section('javascript')
    <script>
        var info_nav = $('.left-nav');
        var detail_nav = $('.right-nav');
        var page_info = $('#page-info');
        var page_detail = $('#page-detail');
        info_nav.on('touchstart', function () {
            $(this).addClass('active');
            detail_nav.removeClass('active');
            page_detail.fadeOut(300, function () {
                page_info.fadeIn(300);
            });
        });

        detail_nav.on('touchstart', function () {
            $(this).addClass('active');
            info_nav.removeClass('active');
            page_info.fadeOut(300, function () {
                page_detail.fadeIn(300);
            });
        });

        $('.consult-btn').click(function () {
            window.location.href = '{{ $active->homeUrl() }}';
        });

        $('.apply-btn').click(function () {
            window.location.href = '{{ $active->applyUrl() }}';
        });

        function apply()
        {
            window.location.href = '{{ $active->applyUrl() }}';
        }

        $('.disable').click(function () {
            $.ajax({
                url: '{{ $active->cancelUrl() }}',
                type: 'get',
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        toastr.error(data.message);
                    } else {
                        toastr.success(data.message);
                        $('.info-footer').html('<button type="button" class="apply-btn" onclick="apply();">参与活动</button>')
                    }
                }
            })
        })
    </script>
@endsection