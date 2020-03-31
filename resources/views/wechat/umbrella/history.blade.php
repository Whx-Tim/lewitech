@extends('wechat.umbrella.common.layout')

@section('title', '借伞记录')

@section('css')
    <style>
        section.container {
            width: 100%;
        }

        ul {
            list-style: none;
            padding: 10px;
        }

        ul li {
            display: block;
            background-color: #fff;
            margin-top: 20px;
            padding: 10px;
        }

        .list-item h4 {
            font-weight: bold;
            font-family: "Lantinghei SC", sans-serif;
            color: #1d1d1d;
            margin-bottom: 2px;
        }
        .list-item h4 img {
            width: 20px;
            height: 20px;
            vertical-align: sub;
            margin-right: 10px;
        }

        .list-item p {
            color: rgb(200,200,200);
            display: block;
            font-size: 12px;
        }

        .list-item .list-item-col {
            display: flex;
            flex-direction: row;
        }

        .list-item .list-item-col span {
            flex: 1;
        }


    </style>
@endsection

@section('content')
    <section class="container">
        <div class="top">
            <img src="{{ asset('images/umbrella/icon-return.png') }}" class="top-icon" id="return-btn">
            <h3>借伞记录</h3>
        </div>
        <ul>
            @foreach($histories as $history)
            <li class="list-item">
                <h4><img src="{{ asset('images/umbrella/icon-borrow.png') }}">{{ $history->borrow_at or '' }}</h4>
                <p class="list-item-row"><span>归还时间：</span>{{ $history->still_at or '' }}</p>
                <p class="list-item-row"><span>持续时间：</span>{{ empty($diffTime = $history->diffTime()) ? '' : $diffTime }}</p>
                {{--<p class="list-item-col"><span>借出站点：{{ $history->borrow_station }}</span><span>归还站点：{{ $history->still_station }}</span></p>--}}
            </li>
            @endforeach
        </ul>
    </section>
    @include('wechat.umbrella.common.footer')
@endsection

@section('javascript')
    <script>
    </script>
@endsection