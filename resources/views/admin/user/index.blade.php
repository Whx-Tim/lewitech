@extends('layouts.admin.app')

@section('admin.title', '用户管理')

@section('breadcrumb')
    <li class="active">用户管理</li>
@endsection

@section('admin.content')
    <div class="row">
        <div class="col-md-12">
            <blockquote>
                <b>用户总数： {{ $count }}</b>
            </blockquote>
        </div>
        <div class="col-md-12">
            <table class="table table-hover Table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>用户openid</th>
                        <th>用户头像</th>
                        <th>用户昵称</th>
                        <th>性别</th>
                        <th>是否关注</th>
                        <th>创建时间</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td><a href="{{$user->adminDetailUrl()}}">{{ $user->openid }}</a></td>
                        @if(!is_null($user->detail))
                            <td><img src="{{ $user->detail->head_img }}" style="height: 120px;width: 120px;border-radius: 120px;"></td>
                            <td>{{ str_limit($user->detail->nickname, 15) }}</td>
                            <td>{{ $user->sex2String() }}</td>
                            <td>{{ $user->subscribe2String() }}</td>
                        @else
                            <td><img src="{{ '/images/no-avatar.png' }}" style="height: 120px;width: 120px;border-radius: 120px;"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        @endif
                        <td>{{ $user->created_at->toDateString() }}</td>
                        <td>
                            <a href="{{ $user->adminEditUrl() }}" operation="edit"><i class="fa fa-pencil fa-2x"></i></a>&nbsp;
                            <a href="{{ $user->adminDeleteUrl() }}" operation="delete"><i class="fa fa-close fa-2x"></i></a>&nbsp;
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination-home">
                {!! $users->links() !!}
            </div>
        </div>
    </div>
@endsection