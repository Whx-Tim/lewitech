@extends('wechat.umbrella.common.layout')

@section('css')
    <style>
        section.container {
            width: 100%;
            background-color: #fff;
            padding: 10px;
            padding-top: 50px;
        }

        .tab {
            display: flex;
            width: 100%;
            box-sizing: border-box;
            position: fixed;
            top: 0;
            left: 0;
            border: none;
        }

        .tab-item.active {
            background-color: #5bd0ce;
            color: #fff;
        }

        .tab-item {
            flex: 1;
            padding: 10px;
            text-align: center;
            border: none;
            background-color: #d4d4d4;
            color: #a6a5a5;
        }

        .page-1,
        .page-2,
        .page-3 {
            display: none;
            width: 100%;
        }

        .page-1.active,
        .page-2.active,
        .page-3.active{
            display: block;
        }

        .banner {
            height: 250px;
            width: 100%;
            box-sizing: border-box;
        }
        .banner img {
            width: 100%;
        }

        .description {
            margin-top: 30px;
        }

        .description h4 {
            text-align: center;
            color: #0cbeaf;
        }
        .description h5 {
            text-align: center;
            font-family: "Lantinghei SC", sans-serif;
            font-weight: bold;
            color: #393939;
        }
        .description p {
            text-indent: 2em;
            font-family: "Lantinghei SC", sans-serif;
            color: #494949;
            box-sizing: border-box;
        }

        #pass-btn {
            color: #fff;
            background-color: #37cd96;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <div class="tab">
            <div class="tab-item active" id="tab-1" link-page="1">押金</div>
            <div class="tab-item" id="tab-2" link-page="2">捐款</div>
            @if(!$is_schoolmate)
            <div class="tab-item" id="tab-3" link-page="3">认证</div>
            @endif
        </div>
        <div class="page-1 active">
            <div class="banner">
                <img src="" alt="我是一张图片">
            </div>
            <div class="description">
                <h4>押金</h4>
                <h5>押金使用方法</h5>
                <p>我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明</p>
            </div>
            <button type="button" class="general-btn" id="deposit-btn">缴纳</button>
        </div>
        <div class="page-2">
            <div class="banner">
                <img src="" alt="我也是一张图片">
            </div>
            <div class="description">
                <h4>公益伞</h4>
                <h5>公益伞说明</h5>
                <p>我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明</p>
            </div>
            <button type="button" class="general-btn" id="donate-btn">捐款</button>
        </div>
        @if(!$is_schoolmate)
            <div class="page-3">
                <form class="umbrella-form">
                    <div class="form-group">
                        <img src="{{ asset('images/umbrella/icon-student_number.png') }}" class="form-group-icon">
                        <input type="text" class="form-group-input" name="student_number" placeholder="请输入学号">
                    </div>
                    <div class="form-group">
                        <img src="{{ asset('images/umbrella/icon-grade.png') }}" class="form-group-icon">
                        <input type="text" class="form-group-input" name="grade" placeholder="请输入入学年份">
                    </div>
                    <div class="form-group">
                        <img src="{{ asset('images/umbrella/icon-college.png') }}" class="form-group-icon">
                        <input type="text" class="form-group-input" name="college" placeholder="请输入学院全称">
                    </div>
                    <button type="button" class="form-submit-btn" id="pass-btn">认证</button>
                </form>
            </div>
        @endif
        @include('wechat.umbrella.common.footer')
    </section>
@endsection

@section('javascript')
    <script>
        $('button[type=button]').click(function () {
            window.location.href = '{{ route('wechat.umbrella.index') }}';
        });

        $('.tab-item').each(function () {
            $(this).on('touchstart', function () {
                var $this = $(this);
                $('.tab-item').each(function () {
                    var $page = $(this).attr('link-page');
                    $(this).removeClass('active');
                    $this.addClass('active');
                })
            })
        });
        $('#tab-1').on('touchstart', function () {
//            $(this).addClass('active');
//            $('#tab-2').removeClass('active');
//            $('#tab-3').removeClass('active');
            $('.page-1').addClass('active');
            $('.page-2').removeClass('active');
            $('.page-3').removeClass('active');
        });
        $('#tab-2').on('touchstart', function () {
//            $(this).addClass('active');
//            $('#tab-1').removeClass('active');
//            $('#tab-3').removeClass('active');
            $('.page-2').addClass('active');
            $('.page-1').removeClass('active');
            $('.page-3').removeClass('active');
        });
        $('#tab-3').on('touchstart', function () {
//            $(this).addClass('active');
//            $('#tab-2').removeClass('active');
//            $('#tab-1').removeClass('active');
            $('.page-3').addClass('active');
            $('.page-2').removeClass('active');
            $('.page-1').removeClass('active');
        });
    </script>
@endsection