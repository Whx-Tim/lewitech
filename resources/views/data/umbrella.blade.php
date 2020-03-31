@extends('layouts.data')

@section('title', '雨伞数据统计')

@section('content')
    <section class="container">
        <table>
            <tbody>
            <tr>
                <td>借出数量</td>
                <td>{{ $borrow_count }}</td>
            </tr>
            <tr>
                <td>归还数量</td>
                <td>{{ $still_count }}</td>
            </tr>
            <tr>
                <td>当前数量</td>
                <td>{{ $station_count }}</td>
            </tr>
            </tbody>
        </table>
    </section>
@endsection
