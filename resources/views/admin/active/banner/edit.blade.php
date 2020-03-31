@extends('layouts.admin.app')

@section('admin.title', '编辑活动banner')

@section('breadcrumb')
    <li><a href="{{ route('admin.active.index') }}">活动管理</a></li>
    <li><a href="{{ route('admin.active.banner.index') }}">banner</a></li>
@endsection

@section('admin.content')
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>活动封面图片</label>
                <img src="http://wj.qn.h-hy.com/{{ $banner->content->path }}" alt="" style="width: 100%;">
                <div id="dropzone" class="dropzone" img-input="path"></div>
            </div>
            <form>
                {!! csrf_field() !!}
                <div class="form-group"><label>网址</label><input type="text" class="form-control" name="address" value="{{ $banner->content->address }}"></div>
                <div class="form-group"><label>顺序</label><input type="number" class="form-control" name="order" placeholder="顺序越低越前，最低为1" value="{{ $banner->content->order }}"></div>
                <input type="hidden" name="path" value="{{ $banner->content->path }}">
                <a href="{{ route('admin.active.banner.edit', ['id' => $banner->id]) }}" id="update-btn" class="btn btn-primary btn-block">添加</a>
            </form>
        </div>
    </div>
@endsection