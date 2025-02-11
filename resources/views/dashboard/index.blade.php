@extends('layouts.app')

@section('title')
Dashboard
@endsection

@if (Auth::user()->lvl == 'admin')
@section('content')
<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 page-container">
    <div class="row d-flex justify-content-between align-items-center">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 page-title">
            <div class="col-6">
                <h1><span class="fa fa-dashboard"></span> Dashboard</h1>
            </div>

            <div class="col-6">
                <h4 class="user-name" style="color: white">Selamat datang - {{ Auth::user()->name }}</h4>
            </div>


        </div>

    </div>

    <div class="page-body menubar">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="row cards-container">
                    <?php $count = 1; ?>
                    @foreach ($data['cards'] as $card)
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="card card-{{ $count++ }}">
                            <div class="card-title">
                                <span class="pull-right icon fa fa-{{$card['icon'] }}"></span>
                                <h3>{{ $card['title'] }}</h3>
                            </div>

                            <div class="card-body">
                                <span>{{ $card['value'] }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- <div class="row" style="margin-top: 50px">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 col-lg-offset-4 col-md-offset-4 col-sm-offset-4">
                <button class="btn-success timetable-btn btn-block" id="start-queue-button">Start Queue Worker</button>
            </div>
        </div> --}}


        <div class="row" style="margin-top: 50px">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 col-lg-offset-4 col-md-offset-4 col-sm-offset-4">
                <button class="btn-primary timetable-btn btn-block" id="resource-add-button">Buat Jadwal Perkuliahan
                    Baru</button>
            </div>
        </div>
    </div>

    <div id="resource-container">
        @include('dashboard.timetables')
    </div>
</div>
@include('dashboard.modals')
@endsection

@elseif (Auth::user()->lvl == 'mahasiswa')
@section('content')
<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 page-container">
    <div class="row d-flex justify-content-between align-items-center">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 page-title">
            <div class="col-6">
                <h1><span class="fa fa-dashboard"></span> Dashboard</h1>
            </div>

            <div class="col-6">
                <h4 class="user-name" style="color: white">Selamat datang - {{ Auth::user()->name }}</h4>
            </div>


        </div>

    </div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <form method="GET" action="{{ route('timetables.index') }}" class="form-inline mb-3">
                <input type="text" name="search" class="form-control" placeholder="Cari jurusan"
                    value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary ml-2">Search</button>
            </form>

            @if (request('search') && count($timetables))
            @foreach ($timetables as $timetable)
            <h3>{{ $timetable['name'] }}</h3>
            <h4>{{ $timetable['period'] }}</h4>
            {!! $timetable['html'] !!}
            <!-- Display the complete timetable HTML -->
            @endforeach
            @else
            @if (request('search'))
            <div class="no-data text-center">
                <p>Tidak ada jadwal ditemukan.</p>
            </div>
            @endif
            @endif
        </div>
    </div>
    {{-- <form method="GET" action="{{ route('searchv2') }}" class="mb-3">
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>pilih jurusan</label>

                <div class="select2-wrapper">
                    <select id="prodi-select" name="prodi" class="form-control select2">
                        <option value="farmasi">Farmasi</option>
                        <option value="d3 kebidanan">D3 Kebidanan</option>
                        <option value="s1 kebidanan">S1 Kebidanan</option>
                        <option value="psikologi">Psikologi</option>
                        <option value="ilmu gizi">Ilmu gizi</option>
                        <option value="kesehatan masyarakat">Kesehatan Masyarakat</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>pilih semester</label>

                <div class="select2-wrapper">
                    <select id="semester-select" name="semester" class="form-control select2">
                        <option value="i">I</option>
                        <option value="ii">II</option>
                        <option value="iii">III</option>
                        <option value="iv">IV</option>
                        <option value="v">V</option>
                        <option value="vi">VI</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="input-group-append">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>

    </form> --}}

    {{-- <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <form method="GET" action="{{ route('search') }}" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or status"
                        value="{{ request()->get('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </div>
            </form>


            <input type="text" id="majorSearch" placeholder="Enter Major (e.g., D3 Kebidanan)">
            <button onclick="searchMajor()">Search</button>
            <div id="result"></div>

            @if (request()->has('search') && request()->get('search') != '')
            @if (count($timetables))
            <table class="table table-bordered">
                <thead>
                    <tr class="table-head">
                        <td>Nama jadwal</td>
                        <td>Status</td>
                        <td style="width: 20%">Actions</td> <!-- Adjusted width for better layout -->
                    </tr>
                </thead>

                <tbody>
                    @foreach ($timetables as $timetable)
                    <tr>
                        <td>{{ $timetable->name }}</td>
                        <td>{{ $timetable->status }}</td>
                        <td>
                            @if($timetable->file_url)
                            <a href="{{ URL::to('/timetables/view/' . $timetable->id) }}"
                                class="btn btn-sm btn-info view-btn" data-id="{{ $timetable->id }}">
                                <span class="fa fa-eye"></span> VIEW
                            </a>
                            <a href="{{ URL::to('/timetables/print/' . $timetable->id) }}"
                                class="btn btn-sm btn-primary print-btn" data-id="{{ $timetable->id }}">
                                <span class="fa fa-print"></span> PRINT
                            </a>
                            @else
                            N/A
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div id="pagination">
                {!!
                $timetables->render()
                !!}
            </div>
            @else
            <div class="no-data text-center">
                <p>Tidak ada jadwal yang ditemukan untuk pencarian: "{{ request()->get('search') }}"</p>
            </div>
            @endif
            @else
            <div class="no-data text-center">
                <p>Silakan masukkan kata kunci untuk mencari jadwal.</p>
            </div>
            @endif
        </div>
    </div> --}}
</div>
@include('dashboard.modals')
@endsection

@elseif (Auth::user()->lvl == 'dosen')
@section('content')
<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 page-container">
    <div class="row d-flex justify-content-between align-items-center">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 page-title">
            <div class="col-6">
                <h1><span class="fa fa-dashboard"></span> Dashboard</h1>
            </div>

            <div class="col-6">
                <h4 class="user-name" style="color: white">Selamat datang - {{ Auth::user()->name }}</h4>
            </div>


        </div>

    </div>

    <div class="page-body menubar">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="row cards-container">
                    <?php $count = 1; ?>
                    @foreach ($data['cards'] as $card)
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="card card-{{ $count++ }}">
                            <div class="card-title">
                                <span class="pull-right icon fa fa-{{$card['icon'] }}"></span>
                                <h3>{{ $card['title'] }}</h3>
                            </div>

                            <div class="card-body">
                                <span>{{ $card['value'] }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- <div class="row" style="margin-top: 50px">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 col-lg-offset-4 col-md-offset-4 col-sm-offset-4">
                <button class="btn-primary timetable-btn btn-block" id="resource-add-button">Buat Jadwal Perkuliahan
                    Baru</button>
            </div>
        </div> --}}
    </div>

    <div id="resource-container">
        @include('dashboard.timetables')
    </div>
</div>
@include('dashboard.modals')
@endsection
@else
<h1>f</h1>

@endif


@section('scripts')
<script src="{{URL::asset('/js/dashboard/index.js')}}"></script>
{{-- <script>
    // resources/js/dashboard/index.js

document.getElementById('start-queue-button').addEventListener('click', function() {
    fetch('/start-queue', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
</script> --}}
@endsection