@extends('wechat.umbrella.common.layout')

@section('title', '测试')

@section('css')
    <style>

        .disabled {
            color: rgb(200,200,200) !important;
        }

        .page-agreement {
            display: block;
            position: fixed;
            width: 95%;
            height: 90%;
            left: 2.5%;
            top: 2.5%;
            background-color: #efefef;
            padding: 20px 10px;
            box-sizing: border-box;
            overflow-y: auto;
            margin-bottom: 40px;
        }

        .btn-area {
            display: flex;
            position: fixed;
            left: 0;
            bottom: 45px;
            width: 100%;
            box-sizing: border-box;
        }

        .btn-area-item {
            flex: 1;
            padding: 10px;
            border: none;
            outline: none;
            background-color: #48acc8;
            color: #ffffff;
            font-size: 16px;
            letter-spacing: .5em;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <div class="top">
            <img src="{{ asset('images/umbrella/icon-return.png') }}" class="top-icon">
            <h3>注册</h3>
        </div>
        <form class="umbrella-form">
            <div class="form-group">
                <img src="{{ asset('images/umbrella/icon-name.png') }}" class="form-group-icon">
                <input type="text" class="form-group-input" name="real_name" placeholder="请输入真实姓名">
            </div>
            <div class="form-group">
                <img src="{{ asset('images/umbrella/icon-id.png') }}" class="form-group-icon">
                <input type="text" class="form-group-input" name="ID_number" placeholder="请输入身份证号">
            </div>
            <div class="form-group">
                <img src="{{ asset('images/umbrella/icon-birthday.png') }}" class="form-group-icon">
                <input type="text" class="form-group-input" name="birthday" placeholder="请输入你的生日" onfocus="blur();" id="date">
            </div>
            <div class="form-group">
                <img src="{{ asset('images/umbrella/icon-phone.png') }}" class="form-group-icon">
                <input type="text" class="form-group-input" name="phone" placeholder="请输入手机号">
            </div>
            <div class="form-group vcode">
                <img src="{{ asset('images/umbrella/icon-vcode.png') }}" class="form-group-icon">
                <input type="text" class="form-group-input" name="vcode" placeholder="请输入验证码">
                <button type="button" class="form-group-button" id="vcode">获取验证码</button>
            </div>
            <div class="form-group">
                <input type="checkbox" name="agreement">&nbsp;我已阅读并且同意<a href="#" id="agree-btn">《公益伞用户协议》</a>
            </div>
            {{--<div class="form-group">--}}
            {{--<img src="{{ asset('images/umbrella/icon-password.png') }}" class="form-group-icon">--}}
            {{--<input type="password" class="form-group-input" name="password" placeholder="请输入密码">--}}
            {{--</div>--}}
            {{--<div class="form-group">--}}
            {{--<img src="{{ asset('images/umbrella/icon-password.png') }}" class="form-group-icon">--}}
            {{--<input type="password" class="form-group-input" name="password_confirmation" placeholder="请再次输入密码">--}}
            {{--</div>--}}
            <button type="button" class="form-submit-btn" id="register-btn">提交注册</button>
        </form>
        @include('wechat.umbrella._user_agreement')
        @include('wechat.umbrella.common.footer')
    </section>
@endsection

@section('javascript')
    <script src="{{ asset('/plugins/datepicker/datePicker.js') }}"></script>
    <script>
        //        $('input[name=birthday]').on('click', function() {
        //            var $this = $(this);
        //            weui.datePicker({
        //                start: 1945,
        //                end: 2017,
        //                defaultValue: [1994,9,26],
        //                onConfirm: function (result) {
        //                    $this.val(String(result).replace(new RegExp(',','gm'), '-'));
        //                }
        //            })
        //        });
        var calendar = new datePicker();
        calendar.init({
            'trigger': '#date',
            'type': 'date',
            'minDate': '1945-1-1',
            'maxDate': '2017-1-1',
            'onSubmit': function() {
                $('#date').val(calendar.value);
            }
        });

        $('.top-icon').click(function () {
            history.go(-1);
        });

        $('#agree-btn').click(function (event) {
            event.preventDefault();
            $('.page-agreement').fadeIn(300);
        });

        $('#cancel-btn').click(function () {
            $('.page-agreement').fadeOut(300);
        });
        $('#submit-btn').click(function () {
            $('.page-agreement').fadeOut(300);
            $('[name=agreement]').attr('checked', true);
        });

        $('#vcode').click(function () {
            $.ajax({
                url: '{{ route('wechat.umbrella.vcode-test') }}',
                type: 'get',
                data: {
                    phone: $('[name=phone]').val()
                },
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        toastr.error(data.message);
                    } else {
                        toastr.success(data.message);
//                        $('[name=vcode]').val(data.data.code);
                    }
                },
                error: function (error) {
                    if (error.status == 422) {
                        var errorMessage = JSON.parse(error.responseText);
                        for(var i in errorMessage) {
                            var errorData = errorMessage[i][0];
                        }
                        toastr.error(errorData);
                    }
                }
            });
            var $this = $(this);
            var limit_time = 60;
            $this.html(limit_time+'秒');
            $this.addClass('disabled');
            var interval = setInterval(function () {
                limit_time--;
                $this.attr('disabled', true);
                $this.html(limit_time+'秒');
                if (limit_time == 0) {
                    $this.attr('disabled', false);
                    $this.removeClass('disabled');
                    $this.html('获取验证码');
                    clearInterval(interval);
                }
            }, 1000);
        });

        $('#register-btn').click(function () {
            console.log();
            if ($('[name=agreement]').is(':checked')) {
                var formData = $('.umbrella-form').serialize();
                $.ajax({
                    url: '{{ route('wechat.umbrella.register') }}',
                    type: 'post',
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        if (data.code) {
                            toastr.error(data.message);
                        } else {
                            toastr.success(data.message);
                            window.location.href = data.data.redirect;
                        }
                    },
                    error: function (error) {
                        if (error.status == 422) {
                            var errorData = JSON.parse(error.responseText);
                            var errorMessage = '';
                            for (var i in errorData) {
                                errorMessage = errorData[i][0];
                            }
                            toastr.error(errorMessage);
                        } else {
                            toastr.error('系统繁忙，请稍后重试');
                        }
                    }
                })
            } else {
                toastr.warning('请勾选我已阅读后才能完成注册');
            }

        })
    </script>
@endsection
