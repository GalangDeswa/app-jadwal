@extends('layouts.app')

@section('title')
Mata kuliah
@endsection

@section('content')
<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 page-container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 page-title">
            <h1><span class="fa fa-book"></span> Mata kuliah</h1>
            <div class="col-6">
                <h4 class="user-name" style="color: white">Selamat datang - {{ Auth::user()->name }}</h4>
            </div>
        </div>
    </div>

    <div class="menubar">
        @include('partials.menu_bar', ['buttonTitle' => 'Tambah mata kuliah'])
    </div>

    <div class="page-body" id="resource-container">
        @include('courses.table')
    </div>
</div>

@include('courses.modals')
@endsection

@section('scripts')
<script src="{{URL::asset('/js/courses/index.js')}}"></script>
@endsection