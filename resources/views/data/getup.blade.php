@extends('layouts.data')

@section('title', '签到打卡数据统计')

@section('css')

@endsection

@section('content')
    <section class="container">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>用户名</th>
                            <th>用户签到总天数</th>
                            <th>用户签到持续天数</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($getups as $getup)
                        <tr>
                            <td>{{ $getup->user->detail->nickname }}</td>
                            <td>{{ $getup->day_sum }}</td>
                            <td>{{ $getup->day_duration }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('javascript')

@endsection