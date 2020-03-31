@extends('layouts.admin.app')

@section('admin.title', $page_title)

@section('admin.content')
    <div class="row">
        <div class="col-md-12">
            <blockquote>
                <b>用户数量：{{ $count }}</b>
            </blockquote>
        </div>
        <div class="col-md-12">
            <table class="table table-hover Table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>用户头像</th>
                        <th>用户昵称</th>
                        <th>性别</th>
                        <th>创建时间</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->user->id }}</td>
                        <td><img src="{{ $user->user->detail->head_img or '/images/no-avatar.png'}}" style="height: 60px;width: 60px;border: none;border-radius: 60px"></td>
                        <td>{{ $user->user->detail->nickname or '' }}</td>
                        <td>{{ $user->user->detail->sex or '' }}</td>
                        <td>{{ $user->created_at->toDateString() }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-home">
            {!! $users->links() !!}
        </div>
        <div class="col-md-12">
            <button type="button" class="btn btn-danger btn-block" id="return-btn">返回</button>
        </div>
    </div>
@endsection

@section('admin.script')
    <script>
        $('#return-btn').click(function () {
            history.go(-1);
        })
    </script>
@endsection