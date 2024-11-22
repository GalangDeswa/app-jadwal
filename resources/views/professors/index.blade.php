@extends('layouts.app')

@section('title')
Dosen
@endsection

@section('content')
<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 page-container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 page-title">
            <h1><span class="fa fa-graduation-cap"></span> Dosen</h1>
            <div class="col-6">
                <h4 class="user-name" style="color: white">Selamat datang - {{ Auth::user()->name }}</h4>
            </div>
        </div>
    </div>

    <div class="menubar">
        @include('partials.menu_bar', ['buttonTitle' => 'Tambah dosen'])
    </div>

    <div class="page-body" id="resource-container">
        @include('professors.table')
    </div>
</div>

@include('professors.modals')
@endsection

@section('scripts')
<script src="{{URL::asset('/js/professors/index.js')}}"></script>
@endsection