@extends('layouts.wechat')

@section('title', '乐微福利')

@section('css')
    <style>
        body,html {
            width: 100%;
            height: 100%;
        }

        section.container {
            width: 100%;
            height: 100%;
            background-image: url("{{ asset('images/temp/insurance_background.png') }}");
            background-repeat: no-repeat;
            background-size: 100% 100%;
            padding: 5%;
            box-sizing: border-box;
        }

        .attention {
            margin: 5%;
            width: 90%;
            height: 80%;
            overflow-y: auto;
            color: #ffffff;
        }

        .attention h4  {
            text-align: center;
        }

        .btn-area {
            text-align: center;
            margin-right: auto;
            margin-left: auto;
        }

        .btn-area button {
            border: none;
            padding: 10px 50px;
            border-radius: 15px;
            outline: none;
        }

        .insurance-btn {
            background-color: #3dd3c3;
        }

        .page-submit {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            z-index: 10;
            box-sizing: border-box;
            background-color: rgba(0,0,0, .4);
            display: none;
        }

        .page-submit form {
            position: absolute;
            top: 25%;
            left: 0;
            height: 40%;
            width: 80%;
            margin-left: 10%;
            padding: 40px 10px;
            box-sizing: border-box;
            z-index: 11;
            background-color: #3dd3c3;
            border: none;
            border-radius: 30px;
        }

        .form-box {
            padding: 10px 5px;

        }
        .form-box label {
            font-size: 13px;
        }
        .form-box input {
            font-size: 13px;
            padding: 5px;
            width: calc(100% - 25%);
            margin-left: 5%;
            outline: none;
            border-radius: 5px;
            border: none;
        }
        .page-submit .btn-area {
            margin-top: 20px;
        }
        .page-submit .btn-area button{
            padding: 10px 35px;
            color: #ffffff;
        }

        .submit-btn {
            background-color: #8ddd5d;
        }

        .cancel-btn {
            background-color: #fa3f1d;
        }

        .tip {
            margin-top: 10px;
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <div class="attention">
            <h4>注意事项</h4>
            <p>1）不同年龄的被投保人所需费用不同，投保时请填写真实的投保人年龄、性别；</p>
            <p>2）为未成年人投保时，投保人必须是父母， 被保险人信息写子女，【身份证】填写处请填出生证信息；</p>
            <p>3）根据被保险人有投保知情权的原则，成年人仅可以为自己和子女投保，不能为其他成年人投保（同一个手机号，同一个微信支付号不能为两个及以上的成年人投保），即您的亲属若需投保需自己扫描海报上的二维码操作，否则保险不能生效；</p>
            <p>4）补贴优惠限1万保额的费用，只补贴首年；如选择自动续保，则往后的费用须用户自行承担；</p>
            <p>5）如选择高于1万的保额，将获得1万保额对应保费的补贴；</p>
        </div>
        <div class="btn-area">
            <button type="button" class="insurance-btn">立即投保</button>
        </div>
    </section>
    <div class="page-submit">
        <form>
            {!! csrf_field() !!}
            <div class="form-box">
                <label>姓名:</label>
                <input type="text" class="form-box-input" name="name" placeholder="请输入投保人的姓名">
            </div>
            <div class="form-box">
                <label>手机:</label>
                <input type="number" class="form-box-input" name="phone" placeholder="请输入投保人的联系手机号码">
            </div>
            <p class="tip">
                投保成功后我们将尽快联系您发放补贴
            </p>
            <div class="btn-area">
                <button type="button" class="cancel-btn" style="margin-right: 5px">取消</button>
                <button type="button" class="submit-btn">确认</button>
            </div>
        </form>
    </div>
@endsection

@section('javascript')
    <script>
        $('.insurance-btn').on('click', function () {
            $('.page-submit').fadeIn(500)
        });

        $('.cancel-btn').on('click', function () {
            $('.page-submit').fadeOut(500)
        });

        $('.submit-btn').on('click', function () {
            var formData = $('form').serialize();
            $.ajax({
                url: '{{ url('wechat/insurance') }}',
                type: 'post',
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        toastr.error(data.message);
                    } else {
                        console.log(data.message);
                        window.location.href = data.data.redirect;
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
    </script>
@endsection