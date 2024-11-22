<!-- Modal for adding a new room -->
<div class="modal custom-modal" id="resource-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">x</span>
                </button>

                <h4 class="modal-heading">Tambah prodi</h4>
            </div>

            <form class="form" method="POST" action="" id="resource-form">
                <input type="hidden" name="_method" value="">
                <div class="modal-body">
                    <div id="errors-container">
                        @include('partials.modal_errors')
                    </div>

                    <div class="row">
                        <div class="col-lg-10 col-md-10 col-sm-10 col-lg-offset-1 col-md-offset-1 col-sm-offset-1">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" name="name" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Mata Kuliah <i class="fa fa-plus side-icon" title="Add Course"
                                        id="course-add"></i></label>

                                <div class="row">
                                    <div class="col-md-4 col-sm-7 col-xs-12">
                                        Mata kuliah
                                    </div>

                                    <div class="col-md-4 col-sm-12 col-xs-12">
                                        Semester
                                    </div>

                                    <div class="col-md-3 col-sm-5 col-xs-12">
                                        Pertemuan per minggu
                                    </div>
                                </div>

                                <div id="courses-container">

                                </div>
                            </div>

                            <div class="form-group">
                                <label>Total mahasiswa</label>
                                <input type="text" name="size" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Ruang yang tidak bisa dipakai</label>

                                <div class="select2-wrapper">
                                    <select id="rooms-select" name="room_ids[]" class="form-control select2" multiple>
                                        <option value="">Pilih ruang</option>
                                        @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-5 col-md-5 col-sm-5 col-offset-1 col-md-offset-1">
                                <button type="button" class="btn btn-danger btn-block"
                                    data-dismiss="modal">Batal</button>
                            </div>

                            <div class="col-lg-5 col-md-5 col-sm-5">
                                <button type="submit" class="btn submit-btn btn-primary btn-block">Tambah</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="course-template" class="hidden">
    <div class="row course-form appended-course" id="course-{ID}-container" style="margin-bottom: 5px">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="select2-wrapper">
                <select class="form-control course-select" name="course-{ID}">
                    <option value="" selected>Pilih mata kuliah</option>
                    @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="select2-wrapper">
                <select class="form-control period-select" name="period-{ID}">
                    <option value="" selected>Pilih semester</option>
                    @foreach ($academicPeriods as $period)
                    <option value="{{ $period->id }}">{{ $period->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-3 col-sm-4 col-xs-10">
            <input type="number" class="form-control course-meetings" name="course-{ID}-meetings">
        </div>

        <div class="col-md-1 col-sm-1 col-xs-2">
            <span class="fa fa-close side-icon course-remove" title="Remove Course" data-id="{ID}"></span>
        </div>
    </div>
</div>