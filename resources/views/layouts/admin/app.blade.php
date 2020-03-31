@extends('layouts.admin.base')

@section('title')@yield('admin.title') - 后台管理@stop

@section('base.content')
{{--    @if(Auth::check())--}}
{{--        @include('layouts.partials.admin-navbar')--}}
    {{--@endif--}}
    <div class="Admin">
        @if(Auth::check())
            @include('layouts.admin.partials.sidebar')
        @endif
        <main class="Container">
            @if(Auth::check())
                @include('layouts.admin.partials.breadcrumb')
            @endif
            @yield('admin.content')

        </main>

    </div>
@stop

@push('head')
<meta name="_token" content="{{ csrf_token() }}">
<link href="https://cdn.bootcss.com/jquery-datetimepicker/2.5.4/jquery.datetimepicker.min.css" rel="stylesheet">
@endpush

@push('scripts.footer')
<script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="{{ asset('js/plugins/classie.js') }}"></script>
<script src="{{ asset('js/plugins/dropzone.min.js') }}"></script>
<script src="{{ asset('js/plugins/summernote.min.js') }}"></script>
<script src="{{ asset('js/plugins/toastr.min.js') }}"></script>
<script src="{{ asset('js/plugins/sweetalert.min.js') }}"></script>
{{--<script src="{{ url('js/plugins.js') }}"></script>--}}
{{--<script src="https://cdn.bootcss.com/jquery-datetimepicker/2.5.4/jquery.datetimepicker.min.js"></script>--}}
{{--<script src="https://cdn.bootcss.com/jquery-datetimepicker/2.5.4/jquery.datetimepicker.js"></script>--}}
<script src="https://cdn.bootcss.com/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.full.js"></script>
{{--<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=2aLkRxKYccsqt8NlvVsgURX99OIUbabz"></script>--}}

<script type="text/javascript">

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "slideDown",
        "hideMethod": "slideUp"
    };

    $('[operation=edit]').tooltip({
        placement: 'top',
        title: '编辑'
    });

    var $check = $('[operation=check]');
    $check.tooltip({
        placement: 'top',
        title: '审核'
    });

    var $delete = $('[operation=delete]');
    $delete.tooltip({
        placement: 'top',
        title: '删除'
    });

    $delete.click(function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        var $this = $(this);
        swal({
            title: '确定删除吗?',
            text: '',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: '确认删除',
            cancelButtonText: '点错了',
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function () {
            $.ajax({
                url: href,
                type: 'get',
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        swal('删除失败',data.message, 'error');
                    } else {
                        swal('删除成功',data.message, 'success');
                        $this.parents('tr').hide(600);
                    }
                },
                error: function (error) {
                    swal('操作异常','','error');
                }
            })
        })
    });

    $check.click(function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        var $this = $(this);
        swal({
            title: '确定审核通过吗?',
            text: '',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: '确认通过',
            cancelButtonText: '点错了',
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function () {
            $.ajax({
                url: href,
                type: 'get',
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        swal('删除失败',data.message, 'error');
                    } else {
                        swal('删除成功',data.message, 'success');
                        $this.parents('tr').children('[status]').html('已审核');
                    }
                },
                error: function (error) {
                    swal('操作异常','','error');
                }
            })
        })
    });

    $('#update-btn').click(function (e) {
        e.preventDefault();
        var formData = $(this).parent().serialize();
        var url = $(this).attr('href');
        swal({
            title: '确定更新吗?',
            text: '',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: '确认更新',
            cancelButtonText: '点错了',
            closeOnConfirm: false
//                showLoaderOnConfirm: true
        }, function () {
            $.ajax({
                url: url,
                type: 'post',
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
//                            toastr.error('保存失败', data.errmsg);
                        swal(data.message,'','error');
                    } else {
//                            toastr.success('保存成功');
                        swal(data.message, '' , 'success');
                    }
                },
                error: function (error) {
                    if (error.status == 422) {
                        var data = JSON.parse(error.responseText);
                        var message;
                        for (var x in data) {
                            message = data[x][0];
                        }
                        swal('添加失败', message, 'error');
                    } else {
                        swal('操作异常','','error');
                    }
                }
            })
        })
    });

    $('#add-btn').click(function (e) {
        e.preventDefault();
        var formData = $(this).parent().serialize();
        var url = $(this).attr('href');
        swal({
            title: '确定添加吗?',
            text: '',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: '确认添加',
            cancelButtonText: '点错了',
            closeOnConfirm: false
//                showLoaderOnConfirm: true
        }, function () {
            $.ajax({
                url: url,
                type: 'post',
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
//                            toastr.error('保存失败', data.errmsg);
                        swal(data.message,'','error');
                    } else {
//                            toastr.success('保存成功');
                        swal(data.message, '' , 'success');
                        console.log(data.data.redirect);
                        window.location.href = data.data.redirect;
                    }
                },
                error: function (error) {
                    if (error.status == 422) {
                        var data = JSON.parse(error.responseText);
                        var message;
                        for (var x in data) {
                            message = data[x][0];
                        }
                        swal('添加失败', message, 'error');
                    } else {
                        swal('操作异常','','error');
                    }

                }
            })
        })
    });

    $('.summernote').summernote();
    var qiniuToken;
    $.ajax({
        url: '{{ route('admin.getUploadToken') }}',
        type: 'get',
        async: 'false',
        dataType: 'json',
        success: function(data) {
            if (data.code) {
                toastr.error(data.message);
            } else {
                qiniuToken = data.data.token;
            }
        }
    });

    var img_input = $('#dropzone').attr('img-input');
    $('.dropzone').dropzone({
        url: 'http://up-z2.qiniu.com/',
        method: 'post',
        paramName: 'file',
        maxFiles: 1,
        maxFileSize: 3,
        acceptedFiles: "image/*",
        addRemoveLinks: true,
        dictDefaultMessage: '拖拽或者点击上传图片',
        dictCancelUpload: '取消',
        dictRemoveFile: '取消',
//        init: function () {
//            this.on("success", function (file) {
//                if (file.status = 'success') {
//                    var path = JSON.parse(file.xhr.responseText).data.path;
//                    $('input[name='+ img_input +']').val(path);
//                    $('#dropzone').prev('img').attr('src',path);
//                } else {
//                    swal('上传失败','','error');
//                }
//            })
//        }
        init: function () {
            this.on("sending", function (file,xhr,formData) {
                formData.append("token", qiniuToken);
            });
            this.on("success", function (file,data) {
                $("[name="+ img_input +"]").val(data.key);
                $('#dropzone').prev('img').attr('src','http://wj.qn.h-hy.com/'+data.key);
//                upload_result("uploadsuccess",data.key);
            });
            this.on("removedfile", function (file) {
                $("[name="+ img_input +"]").val('');
            });
            this.on("error", function (file,errorMessage) {
                console.log(file);
                console.log(errorMessage);
//                if (errorMessage && errorMessage.error)
//                    upload_result("upload error",errorMessage.error);
//                else
//                    upload_result("unknow","");
            });
        }
    });

</script>
@stack('admin.scripts')
@endpush