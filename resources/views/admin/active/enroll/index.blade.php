@extends('layouts.admin.app')

@section('admin.title', '报名用户列表')

@section('breadcrumb')
    <li><a href="{{ route('admin.active.index') }}">活动列表</a></li>
    <li><a href="{{ route('admin.active.detail', ['cache_active' => $active->id]) }}">活动详情</a></li>
@endsection

@section('admin.content')
    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover Table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>姓名</th>
                        <th>联系方式</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td><a href="{{ route('admin.users.detail', ['user' => $user->user_id, 'from' => 'active', 'id' => $active->id]) }}">{{ $user->name }}</a></td>
                        <td>{{ $user->phone }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination-home">
                {!! $users->links()!!}
            </div>
        </div>
    </div>
@endsection