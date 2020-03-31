@extends('layouts.admin.app')

@section('admin.title', '活动详情')

@section('breadcrumb')
    <li><a href="{{ route('admin.active.index') }}">活动管理</a></li>
@endsection

@section('admin.content')
    <div class="row">
        <div class="col-md-12">
            <img src="http://wj.qn.h-hy.com/{{ $active->poster }}" alt="">
        </div>
        <div class="col-md-12">
            <ul class="list-group">
                <li class="list-group-item"><div class="row"><div class="col-md-2">活动名称</div><div class="col-md-10">{{ $active->name }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">发起人</div><div class="col-md-10">{{ $active->sponsor }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">联系电话</div><div class="col-md-10">{{ $active->phone }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">活动地址</div><div class="col-md-10">{{ $active->location }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">活动开始时间</div><div class="col-md-10">{{ $active->start_time }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">活动结束时间</div><div class="col-md-10">{{ $active->end_time }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">活动报名截止</div><div class="col-md-10">{{ $active->end_at }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">活动状态</div><div class="col-md-10">{{ $active->status2String() }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">创建时间</div><div class="col-md-10">{{ $active->created_at }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">更新时间</div><div class="col-md-10">{{ $active->updated_at }}</div></div></li>
            </ul>
        </div>
        <div class="col-md-10 col-md-offset-1">
            <a href="{{ route('admin.active.enroll.index', ['active' => $active->id]) }}" class="btn btn-success btn-block">查看报名用户</a>
        </div>
    </div>
@endsection