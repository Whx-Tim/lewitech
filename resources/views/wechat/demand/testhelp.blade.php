@extends('layouts.wechat')

@section('title', '帮助')

@section('css')
    <style>
        body {
            background-color: #f0f0f0;
        }

        section.container {
            width: 100%;
        }



        form .form-group {
            width: 100%;
            position: relative;
            padding: 5px 0 5px 15px;
            background-color: #ffffff;
            border-bottom: 1px solid #f0f0f0;
            box-sizing: border-box;
        }

        form label {
            display: inline-block;
            font-size: 18px;
            padding-left: 15px;
            position: relative;
        }

        form label:before {
            content: '';
            position: absolute;
            left: 2px;
            top: 4px;
            width: 7px;
            height: 7px;
            border: none;
            border-radius: 7px;
            background-color: #3dd3c3;
        }


        form .form-input {
            width: calc(100% - 80px);
            font-size: 18px;
            padding: 5px 2px;
            margin: 5px 10px;
            border: none;
            display: inline-block;
            color: #d4d4d4;
            outline: none;
        }

        form textarea.form-input {
            width: 100%;
            display: block;
            margin-bottom: 30px;
            resize: none;
        }

        .submit-btn {
            position: fixed;
            bottom: 40px;
            left: 0;
            border: none;
            width: calc(100% - 80px);
            margin-left: 40px;
            background-color: #3dd3c3;
            color: #ffffff;
            font-size: 20px;
            font-weight: bold;
            box-sizing: border-box;
            padding: 10px;
            border-radius: 20px;
            margin-top: 20px;
        }

        .top-title {
            width: 100%;
            box-sizing: border-box;
            text-align: center;
            font-size: 20px;
            padding: 5px;
            background-color: #3dd3c3;
            color: #ffffff;
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <div class="top-title">
            个人信息
        </div>
        <form>
            {!! csrf_field() !!}
            <div class="form-group"><label>称呼</label><input type="text" class="form-input" name="name" placeholder=""></div>
            <div class="form-group"><label>性别</label><input type="text" class="form-input" name="gender" placeholder=""></div>
            <div class="form-group"><label>职业</label><input type="text" class="form-input" name="occupation" placeholder=""></div>
            <div class="form-group"><label>爱好</label><input type="text" class="form-input" name="hobby" placeholder=""></div>
            <div class="form-group"><label>手机</label><input type="text" class="form-input" name="phone" placeholder=""></div>
            <div class="form-group"><label>微信</label><input type="text" class="form-input" name="wechat" placeholder=""></div>
        </form>
        <button type="button" class="submit-btn">确认帮助</button>
    </section>
@endsection

@section('javascript')
    <script>
        $('.submit-btn').click(function () {
            var FormData = $('form').serialize();
            swal({
                title: '确认帮助吗？',
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: '取消',
                confirmButtonText: '确认'
            }, function () {
                $.ajax({
                    url: '',
                    type: 'post',
                    data: FormData,
                    dataType: 'json',
                    success: function (data) {
                        if (data.code) {
                            toastr.error(data.message);
                        } else {
                            toastr.success(data.message);
                            setTimeout(function () {
                                window.location.href = '';
                            },1000);

                        }
                    },
                    error: function (err) {
                        if (err.status = 422) {
                            var errorData = JSON.parse(err.responseText);
                            var errorInfo;
                            for(var i in errorData) {
                                errorInfo = errorData[i][0];
                            }
                            toastr.error(errorInfo);
                        } else {
                            console.log(err.responseText);
                            toastr.error('系统错误，请联系相关管理员');
                        }
                    }
                })
            })

        })
    </script>
@endsection