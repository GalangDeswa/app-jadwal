@extends('layouts.app')

@section('content')

<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 page-container">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title flex-grow-1">{{ $timetableName }}</h1>
    </div>
    <div>
        {!! $timetableData !!}
    </div>
</div>
@endsection