@extends('layouts.data')

@section('content')
    <section class="container">
        <table>
            <thead>
                <tr>
                    <th>站点名称</th>
                    <th>等级数量</th>
                </tr>
            </thead>
            <tbody>
            @foreach($stations as $station)
                <tr>
                    <td>{{ $station->name }}</td>
                    <td>{{ $station->umbrellas_count }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>
@endsection