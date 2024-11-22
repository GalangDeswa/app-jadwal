<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 colxs-12">
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
                        <a href="{{ URL::to('/timetables/viewv2/' . $timetable->id) }}"
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
            <p>Belum ada jadwal yang dibuat</p>
        </div>
        @endif
    </div>
</div>