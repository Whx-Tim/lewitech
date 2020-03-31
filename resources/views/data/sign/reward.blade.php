@extends('layouts.data')

@section('content')
    <section class="container">
        <blockquote>
{{--            <b>总奖金：{{ $total_reward }}</b>--}}
            <b>成功的金额：{{ $success_reward }}</b>
            <b>失败的金额：{{ $fail_reward }}</b>
            <b>可瓜分奖金：{{ $reward }}</b>
            <b>退款金额：{{ $refund_fee }}</b>
        </blockquote>
        <table>
            <thead>
                <tr>
                    <th>名次</th>
                    <th>用户昵称</th>
                    <th>用户头像</th>
                    <th>签到天数</th>
                    <th>早起值</th>
                    <th>可分得奖金</th>
                    <th>累计奖金</th>
                    <th>本次奖金</th>
                </tr>
            </thead>
            <tbody>
            @foreach($signInfos as $key => $signInfo)
                <tr>
                    <td>{{ ($key+1) }}</td>
                    <td>{{ $signInfo->user->detail->nickname }}</td>
                    <td><img src="{{ $signInfo->user->detail->head_img }}" width="100"></td>
                    <td>{{ $signInfo->duration_count }}</td>
                    <td>{{ $signInfo->time_value }}</td>
                    <td>{{ $signInfo->reward }}</td>
                    <td>{{ $signInfo->total_reward }}</td>
                    <td>{{ $signInfo->now_reward }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>
@endsection