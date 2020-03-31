@extends('layouts.admin.app')

@section('admin.title', '用户详情')

@section('breadcrumb')
    <li><a href="{{ $path['url'] }}">{{ $path['name'] }}</a></li>
@endsection

@section('admin.content')
    <div class="row">
        <div class="col-md-12">
            <img src="{{ $user->detail->head_img }}" alt="">
        </div>
        <div class="col-md-12">
            <ul class="list-group">
                <li class="list-group-item"><div class="row"><div class="col-md-2">用户昵称</div><div class="col-md-10">{{ $user->detail->nickname }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">用户性别</div><div class="col-md-10">{{ $user->detail->sex2String() }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">用户所在城市</div><div class="col-md-10">{{ $user->detail->city }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">用户所在国家</div><div class="col-md-10">{{ $user->detail->country }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">是否关注公众号</div><div class="col-md-10">{{ $user->detail->subscribe2string() }}</div></div></li>
                <li class="list-group-item"><div class="row"><div class="col-md-2">是否是股东</div><div class="col-md-10">{{ $user->detail->is_shareholder() }}</div></div></li>
            </ul>
        </div>
    </div>
@endsection

