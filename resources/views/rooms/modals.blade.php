<!-- Modal for adding a new room -->
<div class="modal custom-modal" id="resource-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">x</span>
                </button>

                <h4 class="modal-heading">Tambah ruang kelas</h4>
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
                                <label>Ruang</label>
                                <input type="text" name="name" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Kapasitas</label>
                                <input type="number" name="capacity" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Tipe ruang</label>

                                <div class="select2-wrapper">
                                    <select id="roomtype-select" name="room_type" class="form-control select2">
                                        <option value="">Pilih Tipe ruang</option>
                                        <option value="reguler">Reguler</option>
                                        <option value="lab-kes">Lab Praktikum ilmu kesehatan</option>
                                        <option value="lab-kom">Lab Praktikum komputer</option>
                                        <option value="lab-far">Lab Praktikum farmasi</option>
                                        {{-- <option value="magang">Magang</option>
                                        <option value="KKN">Kuliah kerja nyata</option> --}}
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
                                <button type="submit" class="submit-btn btn btn-primary btn-block">Tambah</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>