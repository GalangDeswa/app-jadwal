<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        @if (count($professors))
        <table class="table table-bordered">
            <thead>
                <tr class="table-head">
                    <th style="width: 20%">Nama</th>
                    <th style="width: 20%">Email</th>
                    <th style="width: 30%">Mata kuliah</th>
                    <th style="width: 20%">Sesi yang tidak bisa</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($professors as $professor)
                <tr>
                    <td>{{ $professor->name }}</td>
                    <td>{{ $professor->email }}</td>
                    <td>
                        @if (count($professor->courses))
                        <ul>
                            @foreach ($professor->courses as $course)
                            <li>{{ $course->course_code . " " . $course->name }}</li>
                            @endforeach
                        </ul>
                        @else
                        <p>Belum ada mata kuliah</p>
                        @endif
                    </td>
                    <td>
                        @if (count($professor->unavailable_timeslots))
                        <ul>
                            @foreach ($professor->unavailable_timeslots as $period)
                            <li>{{ $period->day->name . " " . $period->timeslot->time }}</li>
                            @endforeach
                        </ul>
                        @else
                        <p>Tidak ada sesi kosong</p>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm resource-update-btn" data-id="{{ $professor->id }}"><i
                                class="fa fa-pencil"></i></button>
                        <button class="btn btn-danger btn-sm resource-delete-btn" data-id="{{ $professor->id }}"><i
                                class="fa fa-trash-o"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div id="pagination">
            {!!
            $professors->render()
            !!}
        </div>
        @else
        <div class="no-data text-center">
            <p>Tidak ada data</p>
        </div>
        @endif
    </div>
</div>