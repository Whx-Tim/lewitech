@extends('layouts.temp')

@section('javascript')
    <script>
        window.location.href = '{{ $redirect }}';
    </script>
@endsection