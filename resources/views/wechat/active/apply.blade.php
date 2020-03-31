@extends('layouts.wechat')

@section('title', '活动报名')

@section('css')
    <link rel="stylesheet" href="{{ url('css/active/apply.css') }}">
@endsection

@section('content')
    <section class="container">
        <form id="mainForm">
            <input type="text" value="基本信息" disabled>
            <input type="text" name="name" placeholder="姓名">
            <input type="number" name="phone" placeholder="联系方式">
            <input type="hidden" name="user_id" value="{{ Auth::id() }}">
        </form>
        <div class="footer-btn">
            <button type="button" class="submit-btn">确认提交</button>
        </div>
    </section>
@endsection

@section('javascript')
    <script>
        $('.submit-btn').click(function () {
            var formData = $('#mainForm').serialize();
            $.ajax({
                url: '{{ $active->applyUrl() }}',
                type: 'post',
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        toastr.error('',data.message);
                    } else {
                        toastr.success('', data.message);
                        alert('报名成功');
                        window.location.href = '{{ $active->detailUrl() }}'
                    }

                }
            })
        })

    </script>
@endsection