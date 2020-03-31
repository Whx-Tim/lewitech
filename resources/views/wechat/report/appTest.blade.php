@extends('layouts.wechat')

@section('title', 'app测试结果反馈')

@section('css')

@endsection

@section('content')
    <section class="container">
        <form>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <textarea name="content" rows="10" class="weui-textarea" placeholder="请输入使用app的过程中，遇到的问题与不好的地方，提出修改的建议，我们会第一时间收集您的建议进行需求讨论，然后做更加优化的提升"></textarea>
                </div>
            </div>
        </form>
        <div class="btn-area">
            <button type="button" class="weui-btn weui-btn_primary" id="submit-btn">提交内测建议</button>
            <a href="{{ route('wechat.report.my') }}" class="weui-btn weui-btn_primary">查看我提交过的建议</a>
        </div>
    </section>
@endsection

@section('javascript')
    <script>
        $('#submit-btn').click(function () {
            var formData = $('form').serialize();
            $.ajax({
                url: '{{ route('wechat.report.app_test') }}',
                type: 'post',
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        toastr.error(data.message);
                    } else {
                        toastr.success(data.message);
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
            })
        })
    </script>
@endsection