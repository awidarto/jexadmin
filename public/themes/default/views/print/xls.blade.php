@extends('layout.yukonxls')

@section('content')

@foreach($tables as $table)

    {{ $table }}

@endforeach


@stop

@section('modals')

@stop