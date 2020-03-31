@extends('layouts.wechat')

@section('title', '需求详情')

@section('css')
    <style>
        body {
            width: 100%;
            box-sizing: border-box;
            background-image: url("/images/demand/background.png");
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }

        section.container {
            width: 100%;
            box-sizing: border-box;
        }

        .demand-title {
            text-align: center;
        }
        .demand-title h2{
            font-size: 25px;
            /*background-color: #3dd3c3;*/
            color: #ffffff;
            padding: 5px 0;
            word-wrap: break-word;
            font-weight: bold;
            box-sizing: border-box;
            text-align: center;
        }
        .demand-title img {
            height: 100px;
            width: 100px;
            border-radius: 50%;
            border: none;
            margin-top: 10px;
        }

        .demand-content{
            font-size: 18px;
            margin-bottom: 10px;
            margin-top: 10px;
            width: 80%;
            margin-left: 10%;
            background-color: #ffffff;
            color: #3c3c3c;
            padding: 5px;
            text-indent: 2em;
            word-wrap: break-word;
            box-sizing: border-box;
        }

        .demand-title span,
        .demand-content span{
            font-size: 18px;
            margin-bottom: 10px;
        }



        .help-list {
            list-style: none;
        }
        .help-list .help-list-item {
            min-height: 100px;
            width: 100%;
            display: inline-block;
            background-color: #ffffff;
            color: #b1b1b1;
            font-size: 15px;
            margin-bottom: 5px;
            position: relative;
        }
        .help-list .help-list-item .user-avatar {
            height: 90px;
            padding: 10px;
            width: 90px;
            float: right;
            margin-right: 5px;
            box-sizing: border-box;
            display: inline-block;
        }
        .help-list .help-list-item .user-info {
            box-sizing: border-box;
            padding: 10px;
            display: inline-flex;
            flex-direction: column;
            width: calc(100% - 95px);
            min-height: 100px;
            clear: both;
        }
        .user-info .info-item {
            flex: 1;
        }

        .info-item h4 {
            font-size: 22px;
            /*color: #353535;*/
        }

        .info-item p span,
        .info-item span,
        .user-contact p span{
            /*color: #353535;*/
            margin-right: 5px;
        }

        .info-item p {
            flex: 1;
        }

        .user-avatar img {
            height: 80px;
            width: 80px;
            border-radius: 50%;
            border: none;
        }

        .user-basic {
            border-bottom: 1px solid #bfbfbf;
        }

        .user-contact {
            padding: 10px;
            display: flex;
            flex-direction: row;
        }

        .user-contact p {
            flex: 1;
        }

        .demand-btn,
        .disabled-btn{
            border: none;
            width: calc(100% - 20px);
            margin-left: 10px;
            background-color: #7ffff4;
            color: #7a7676;
            font-size: 20px;
            font-weight: bold;
            box-sizing: border-box;
            padding: 15px;
            border-radius: 30px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .check-btn,
        .refuse-btn{
            border: none;
            width: calc(100% - 20px);
            margin-left: 10px;
            background-color: #afafaf;
            color: #ffffff;
            font-size: 20px;
            font-weight: bold;
            box-sizing: border-box;
            padding: 15px;
            border-radius: 30px;
            margin-top: 20px;
        }

        .demand-logo {
            width: 100%;
            box-sizing: border-box;
            text-align: center;
            background-color: rgba(85,128,123, .8);
        }
        .demand-logo img {
            height: 40px;
        }

        h4 span.schoolmate {
            position: relative;
        }
        h4 span.schoolmate:after {
            content: '深大校友';
            position: absolute;
            right: -55px;
            top: 2px;
            font-size: 12px;
            border: 1px solid #ef3865;
            border-radius: 5px;
            color: #ef3865;
        }

    </style>
@endsection

@section('content')
    <section class="container">
        <div class="demand-logo">
            <img src="{{ asset('images/demand/logo.png') }}">
        </div>
        <div class="demand-title">
            <img src="{{ $demand->user->detail->head_img or '/images/no-avatar.png' }}">
            <h2>{{ $demand->title }}</h2>
        </div>
        <div class="demand-content"><p>{{ $demand->content }}</p></div>
        <ul class="help-list">
            <li class="help-list-item" style="background-color: rgba(255,255,255,0);color: #ffffff">
                <div class="user-basic">
                    {{--<div class="user-avatar"><img src="{{ $demand->user->detail->head_img or '/images/no-avatar.png' }}"></div>--}}
                    <div class="user-info" style="width: 100%;">
                        <div class="info-item" style="text-align: center;margin-bottom: 10px">
                            <h4 style="text-align: center;border-bottom: 1px solid rgb(255,255,255);"><span class="{{ !empty($demand->data->is_schoolmate) ? $demand->data->is_schoolmate == 1 ? 'schoolmate' : '' : '' }}">{{ $demand->data->name or $demand->name }}</span></h4>
                        </div>
                        <div class="info-item" style="display: flex; flex-direction: row;padding: 0 20px">
                            <p><span>性别</span>{{ $demand->data->gender or '' }}</p>
                            <p><span>年龄</span>{{ $demand->data->age or '' }}</p>
                            <p><span>职业</span>{{ $demand->data->occupation or '' }}</p>
                        </div>
                        <div class="info-item" style="padding: 0 20px">
                            <span>爱好</span>
                            {{ $demand->data->hobby or '' }}
                        </div>
                        @if(!empty($demand->data->is_schoolmate))
                            @if($demand->data->is_schoolmate == 1)
                                <div class="info-item" style="display: flex; flex-direction: column;padding: 0 20px">
                                    <p><span>学号</span>{{ empty($demand->data->student_number) ? '' : $demand->data->student_number }}</p>
                                    <p><span>学院</span>{{ empty($demand->data->college) ? '' : $demand->data->college}}</p>
                                    <p><span>入学年份</span>{{ empty($demand->data->grade) ? '' : $demand->data->grade}}&nbsp;级</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
                @if(!empty($is_help) | Auth::user()->adminset >= 5 | $is_self)
                    <div class="user-contact" style="padding: 0 30px;">
                        <p><span>手机号</span>{{ $demand->data->phone or $demand->phone }}</p>
                        <p><span>微信</span>{{ $demand->data->wechat or '' }}</p>
                    </div>
                @endif
            </li>
        </ul>
        @if(!$is_self)
            @if(!$is_help)
                @if($is_check == 1)
                    <button type="button" class="demand-btn">我可以帮助</button>
                @else
                    <button type="button" class="demand-btn" disabled>还未审核，还不可以帮助</button>
                @endif
            @else
                <button type="button" class="disabled-btn" disabled>已帮助</button>
            @endif
        @else
            @if(!is_null($demand->enrolls))
                <ul class="help-list">
                    @foreach($demand->enrolls as $enroll)
                        <?php $data = is_null($enroll->data) ? false : json_decode($enroll->data); ?>
                        <li class="help-list-item">
                            <div class="user-basic">
                                <div class="user-avatar"><img src="{{ $enroll->user->detail->head_img or '/images/no-avatar.png' }}"></div>
                                <div class="user-info">
                                    <div class="info-item">
                                        <h4>{{ $data->name or $enroll->name }}</h4>
                                    </div>
                                    <div class="info-item" style="display: flex; flex-direction: row">
                                        <p><span>性别</span>{{ $data->gender or '' }}</p>
                                        <p><span>年龄</span>{{ $data->age or '' }}</p>
                                        <p><span>职业</span>{{ $data->occupation or '' }}</p>
                                    </div>
                                    <div class="info-item">
                                        <span>爱好</span>
                                        {{ $data->hobby or '' }}
                                    </div>
                                </div>
                            </div>
                            <div class="user-contact">
                                <p><span>手机号</span>{{ $data->phone or $enroll->phone }}</p>
                                <p><span>微信</span>{{ $data->wechat or '' }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        @endif
        @if(Auth::user()->adminset >= 5)
            @if($is_check)
                <button type="button" class="check-btn" disabled>已审核</button>
            @else
                <button type="button" class="check-btn">审核</button>
            @endif
            @if($is_refuse)
                <button type="button" class="refuse-btn" disabled>已拒绝</button>
            @else
                <button type="button" class="refuse-btn">拒绝</button>
            @endif
        @endif
    </section>
@endsection

@section('javascript')
    <script>
        $('.demand-btn').click(function () {
            window.location.href = '{{ $demand->helpUrl() }}';
        });

        $('.refuse-btn').click(function () {
            swal({
                title: '确认拒绝吗？',
                text: '拒绝对方，并向对方放出一段狠话！',
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: '取消',
                confirmButtonText: '确认'
            }, function () {
                $.ajax({
                    url: '{{ url('wechat/demand/refuse/'.$demand->id) }}',
                    type: 'get',
                    dataType: 'json',
                    success: function (data) {
                        if (data.code) {
                            swal(data.message, '', 'error');
                        } else {
                            swal(data.message, '', 'success');
                            window.location.reload();
                        }
                    },
                    error: function (error) {
                        swal('系统错误，请联系管理员','', 'error');
                        console.log(error.responseText);
                    }
                })
            })
        });

        $('.check-btn').click(function () {
            swal({
                title: '确认审核通过?',
                text: '',
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: '取消',
                confirmButtonText: '确认'
            }, function () {
                $.ajax({
                    url: '{{ url('wechat/demand/check/'.$demand->id) }}',
                    type: 'get',
                    dataType: 'json',
                    success: function (data) {
                        if (data.code) {
                            toastr.error(data.message);
                        } else {
                            toastr.success(data.message);
                            window.location.reload();
                        }
                    },
                    error: function (error) {
                        toastr.error('系统错误，请联系管理员');
                        console.log(error.responseText);
                    }
                })
            })
        })
    </script>
@endsection