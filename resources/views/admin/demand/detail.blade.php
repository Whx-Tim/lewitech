@extends('layouts.admin.app')

@section('admin.title', '需求详情')

@section('breadcrumb')
    <li><a href="{{ route('admin.demand.index') }}">需求管理</a></li>
@endsection

@section('admin.content')
    <div class="row">
        <div class="col-md-6"><a href="{{ $demand->adminViewUsersUrl() }}"><div class="Overview Panel"><div class="Content overview-content"><div class="overview-title"><p class="counter">{{ $demand->viewUsers()->count() }}</p><span>浏览数量</span></div><div class="overview-icon"><i class="fa fa-eye"></i></div></div></div></a></div>
        <div class="col-md-6"><a href="{{ $demand->adminEnrollsUsersUrl() }}"><div class="Overview Panel"><div class="Content overview-content"><div class="overview-title"><p class="counter">{{ $demand->enrolls()->count() }}</p><span>报名数量</span></div><div class="overview-icon"><i class="fa fa-user"></i></div></div></div></a></div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <ul class="list-group">
                <li class="list-group-item"><div class="row"><div class="col-md-2">标题</div><div class="col-md-10">{{ $demand->title }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">联系电话</div><div class="col-md-10">{{ $demand->phone }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">联系称呼</div><div class="col-md-10">{{ $demand->name }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">发布用户</div><div class="col-md-10">{{ $demand->user->detail->nickname }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">状态</div><div class="col-md-10">{{ $demand->status2String() }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">创建时间</div><div class="col-md-10">{{ $demand->created_at }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">修改时间</div><div class="col-md-10">{{ $demand->updated_at }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">需求内容</div><div class="col-md-10">{{ $demand->content }}</div></div></li>
            </ul>
        </div>
    </div>
@endsection