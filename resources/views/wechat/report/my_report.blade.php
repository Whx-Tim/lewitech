@extends('layouts.wechat')

@section('title', '我的反馈')

@section('css')
    <style>
        ul {
            list-style: none;
        }
        ul li {
            display: block;
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            border-bottom: 1px solid rgb(200,200,200);
        }

        ul li h4 {
            font-size: 18px;
        }

        ul li p {
            font-size: 14px;
            color: rgb(200,200,200);
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <ul>
            @foreach($reports as $report)
            <li>
                <h4>{{ $report->data }}</h4>
                <p>{{ $report->created_at }}</p>
            </li>
            @endforeach
        </ul>
    </section>
@endsection