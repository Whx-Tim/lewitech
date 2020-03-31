@extends('layouts.wechat')

@section('title', '需求详情')

@section('css')
    <style>
        body {
            background-color: #f8f8f8;
        }

        section.container {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        .demand-title h2{
            font-size: 25px;
            background-color: #3dd3c3;
            color: #ffffff;
            padding: 15px 0 15px 5px;
            word-wrap: break-word;
            font-weight: bold;
            box-sizing: border-box;
        }

        .demand-content{
            font-size: 18px;
            margin-bottom: 10px;
            margin-top: 10px;
            background-color: #eeeeee;
            color: #353535;
            padding: 5px;
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
            width: calc(100% - 100px);
            height: 100px;
            clear: both;
        }
        .user-info .info-item {
            flex: 1;
        }

        .info-item h4 {
            font-size: 18px;
            color: #353535;
        }

        .info-item p span,
        .info-item span,
        .user-contact p span{
            color: #353535;
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
            background-color: #3dd3c3;
            color: #ffffff;
            font-size: 20px;
            font-weight: bold;
            box-sizing: border-box;
            padding: 15px;
            border-radius: 30px;
            margin-top: 20px;
        }

        .check-btn {
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
        }
        .demand-logo img {
            height: 40px;
        }

    </style>
@endsection

@section('content')
    <section class="container">
        <div class="demand-logo">
            <img src="{{ asset('images/demand/lewitech_logo.png') }}" alt="">
        </div>
        <div class="demand-title"><h2>标题</h2></div>
        <div class="demand-content"><p>内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容</p></div>
        <ul class="help-list">
            <li class="help-list-item">
                <div class="user-basic">
                    <div class="user-avatar"><img src="/images/no-avatar.png"></div>
                    <div class="user-info">
                        <div class="info-item">
                            <h4>你的名字</h4>
                        </div>
                        <div class="info-item" style="display: flex; flex-direction: row">
                            <p><span>性别</span>女</p>
                            <p><span>年龄</span>18</p>
                            <p><span>职业</span>画家</p>
                        </div>
                        <div class="info-item">
                            <span>爱好</span>
                            爱好爱好爱好爱好爱好爱好爱好爱好爱好爱好
                        </div>
                    </div>
                </div>
                <div class="user-contact">
                    <p><span>手机号</span>13418866733</p>
                    <p><span>微信</span>XIN13418866733</p>
                </div>
            </li>
        </ul>
    </section>
@endsection

@section('javascript')
    <script>
        $('.demand-btn').click(function () {
            window.location.href = '';
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
                    url: '',
                    type: 'get',
                    dataType: 'json',
                    success: function (data) {
                        if (data.code) {
                            toastr.error(data.message);
                        } else {
                            toastr.success(data.message);
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