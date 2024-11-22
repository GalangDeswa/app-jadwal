<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        @if (count($courses))
        <table class="table table-bordered">
            <thead>
                <tr class="table-head">
                    <th style="width: 30%">Kode Matkul</th>
                    <th style="width: 30%">Mata kuliah</th>
                    <th style="width: 30%">Dosen</th>
                    <th style="width: 30%">SKS</th>
                    <th style="width: 30%">Tipe</th>
                    <th style="width: 10%">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach($courses as $course)
                <tr>
                    <td>{{ $course->course_code }}</td>
                    <td>{{ $course->name }}</td>
                    <td>
                        <ul>
                            @foreach ($course->professors as $professor)
                            <li>{{ $professor->name }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>{{ $course->credit }}</td>
                    <td>{{ $course->course_type }}</td>
                    <td>
                        <button class="btn btn-primary btn-sm resource-update-btn" data-id="{{ $course->id }}"><i
                                class="fa fa-pencil"></i></button>
                        <button class="btn btn-danger btn-sm resource-delete-btn" data-id="{{ $course->id }}"><i
                                class="fa fa-trash-o"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div id="pagination">
            {!!
            $courses->render()
            !!}
        </div>
        @else
        <div class="no-data text-center">
            <p>Tidak ada data</p>
        </div>
        @endif
    </div>
</div>