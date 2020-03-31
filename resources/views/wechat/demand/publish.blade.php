@extends('layouts.wechat')

@section('title', '需求发布')

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
            display: inline-block;
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
            width: calc(100% - 140px);
            font-size: 18px;
            padding: 5px 2px;
            margin: 5px 10px;
            border: none;
            display: inline-block;
            color: #d4d4d4;
            outline: none;
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
    </style>
@endsection

@section('content')
<section class="container">
    <form>
        {!! csrf_field() !!}
        <div class="form-group"><label>标题</label><input type="text" class="form-input" name="title" placeholder=""></div>
        <div class="form-group"><label>内容</label><textarea name="content" rows="10" class="form-input"></textarea></div>
        <div class="form-group"><label>称呼</label><input type="text" class="form-input" name="name" placeholder=""></div>
        <div class="form-group"><label>性别</label><input type="text" class="form-input" name="gender" placeholder=""></div>
        <div class="form-group"><label>年龄</label><input type="text" class="form-input" name="age" placeholder=""></div>
        <div class="form-group"><label>职业</label><input type="text" class="form-input" name="occupation" placeholder=""></div>
        <div class="form-group"><label>爱好</label><input type="text" class="form-input" name="hobby" placeholder=""></div>
        <div class="form-group"><label>手机</label><input type="text" class="form-input" name="phone" placeholder=""></div>
        <div class="form-group"><label>微信</label><input type="text" class="form-input" name="wechat" placeholder=""></div>
        <div class="weui-cell weui-cell_switch">
            <div class="weui-cell__bd" style="color: black">是否是深大校友</div>
            <div class="weui-cell__ft">
                <input type="checkbox" class="weui-switch" id="schoolmate">
            </div>
        </div>
        <div id="schoolmate-input">
            <div class="form-group"><label>学号</label><input type="text" class="form-input" name="student_number" placeholder=""></div>
            <div class="form-group"><label>学院</label><input type="text" class="form-input" name="college" placeholder=""></div>
            <div class="form-group"><label>入学年份</label><input type="text" class="form-input" name="grade" onfocus="blur();" placeholder="请直接填入数字"></div>
            <input type="hidden" name="is_schoolmate" value="0">
        </div>
    </form>
    <button type="button" class="submit-btn">发布需求</button>
</section>
@endsection

@section('javascript')
    <script>
        $('#schoolmate').on('change', function () {
            var $this = $('[name=is_schoolmate]');
            var val = $this.val();
            if (val == '1') {
                $this.val('0');
                $('#schoolmate-input').hide(300);
            } else {
                $this.val('1');
                $('#schoolmate-input').show(300);
            }
        });

        $('.submit-btn').click(function () {
            var FormData = $('form').serialize();
            swal({
                title: '确认发布？',
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: '取消',
                confirmButtonText: '确认'
            }, function () {
                $.ajax({
                    url: '/wechat/demand/publish',
                    type: 'post',
                    data: FormData,
                    dataType: 'json',
                    success: function (data) {
                        if (data.code) {
                            toastr.error(data.message);
                        } else {
                            toastr.success(data.message);
                            setTimeout(function () {
                                window.location.href = data.data.url;
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
        });

        $('[name=grade]').on('click', function () {
            var $this = $(this);
            yearPicker(1983,2017, 2013, function (result) {
                $this.val(result);
            });
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