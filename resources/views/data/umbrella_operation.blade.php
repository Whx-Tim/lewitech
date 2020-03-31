@extends('layouts.data')

@section('content')
    <section class="container">
        @foreach($operations as $operation)
            <div class="row">
                {{ $operation }}
            </div>
        @endforeach
    </section>
@endsection