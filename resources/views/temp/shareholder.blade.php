@extends('layouts.wechat')

@section('title', '股东简介')

@section('css')
    <link href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
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
            display: block;
            font-size: 18px;
            padding-left: 15px;
            position: relative;
        }

        form label:before {
            content: '';
            position: absolute;
            left: 2px;
            top: 10px;
            width: 7px;
            height: 7px;
            border: none;
            border-radius: 7px;
            background-color: #3dd3c3;
        }


        form .form-input {
            width: calc(100% - 25px);
            font-size: 18px;
            padding: 5px 2px;
            margin: 5px 10px;
            border: none;
            display: inline-block;
            color: #d4d4d4;
            outline: none;
            box-sizing: border-box;
        }

        form textarea.form-input {
            resize: none;
            vertical-align: text-top;
        }

        .submit-btn {
            /*position: fixed;*/
            /*bottom: 40px;*/
            display: block;
            /*left: 0;*/
            border: none;
            width: calc(100% - 80px);
            background-color: #3dd3c3;
            color: #ffffff;
            font-size: 20px;
            font-weight: bold;
            box-sizing: border-box;
            padding: 10px;
            border-radius: 20px;
            margin: 20px 0 20px 40px;
        }

        #schoolmate-input {
            display: none;
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
            股东简介
        </div>
        <form>
            <div class="form-group"><label>姓名</label><input type="text" class="form-input" name="name" placeholder=""></div>
            <div class="form-group"><label>性别</label><input type="text" class="form-input" name="sex" placeholder=""></div>
            <div class="form-group"><label>出生年份</label><input type="text" class="form-input" name="birthday_year" placeholder=""></div>
            <div class="form-group"><label>现工作单位及职务</label><input type="text" class="form-input" name="position" placeholder=""></div>
            <div class="form-group"><label>社会组织担任职务</label><input type="text" class="form-input" name="social" placeholder=""></div>
            <div class="form-group"><label>企业简介</label><textarea name="undertaking_introduction" class="form-input" placeholder="请填写您的企业或是您所在的企业单位的相关信息"></textarea></div>
            <div class="form-group"><label>企业供需内容</label><textarea name="undertaking_content" class="form-input" placeholder="请填写您的企业或是您所在的企业单位的相关信息"></textarea></div>
            <div class="form-group"><label>个人履历</label><textarea name="experience" class="form-input" rows="5" placeholder="19xx xx大学xx学院毕业                       19xx ~ 19xx xx企业,工作职务,工作内容              19xx ~ 19xx xx企业,工作职务, 工作内容"></textarea></div>
            <div class="form-group"><label>个人爱好</label><input type="text" class="form-input" name="hobby" placeholder=""></div>
            <div class="form-group"><label>自我评价</label><input type="text" class="form-input" name="self_comment" placeholder=""></div>
            <div class="form-group"><label>我心中的乐微</label><textarea name="lewitech" class="form-input" rows="5" placeholder="1、为什么成为乐微股东                       2、对乐微有什么期许"></textarea></div>
        </form>
        <button type="button" class="submit-btn">确认提交</button>
    </section>
@endsection

@section('javascript')
    <script>

        $('.submit-btn').click(function () {
            var FormData = $('form').serialize();
            swal({
                title: '确认提交？',
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: '取消',
                confirmButtonText: '确认'
            }, function () {
                $.ajax({
                    url: '{{ route('wechat.temp.apply', ['type' => 'shareholder']) }}',
                    type: 'post',
                    data: FormData,
                    dataType: 'json',
                    success: function (data) {
                        if (data.code) {
                            toastr.error(data.message);
                        } else {
                            toastr.success(data.message);
                            if (typeof (data.data.redirect) != 'undefined') {
                                setTimeout(function () {
                                    window.location.href = data.data.redirect;
                                },1000);
                            }
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
        });

//        $('input[name=birthday_year]').on('click', function () {
//            var $this = $(this);
//            yearPicker(1960,2000, 1983, function (result) {
//                $this.val(result);
//            });
//        });

        $('input[name=sex]').on('click', function () {
            var $this = $(this);
            var array = new Array();
            array[0] = {
                label: '男',
                value: '男',
            };
            array[1] = {
                label: '女',
                value: '女'
            };
            weui.picker(array, {
                onConfirm: function (result) {
                    $this.val(result);
                }
            })
        });

        function yearPicker(start, end, defaultValue, callback) {
            var array = new Array();
            var j = 0;
            for (var i = start; i <= end; i++) {
                array[j] = {
                    label: i,
                    value: i
                };
                j++;
            }
            weui.picker(array, {
                defaultValue: defaultValue,
                onConfirm: function (result) {
                    callback(result);
                }
            })
        }
    </script>
@endsection