<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        @if (count($timetables))
        <table class="table table-bordered">
            <thead>
                <tr class="table-head">
                    <td>Nama jadwal</td>
                    <td style="width: 10%">Status</td>
                    <td style="width: 20%">Progress</td> <!-- Added Progress column -->
                    <td style="width: 30%">Actions</td>
                </tr>
            </thead>

            <tbody>
                @foreach ($timetables as $timetable)
                <tr>
                    <td>{{ $timetable->name }}</td>
                    <td style="white-space: nowrap;">{{ $timetable->status }}</td>
                    <td>
                        <div class="progress">
                            <div id="progress-bar-{{ $timetable->id }}" class="progress-bar" role="progressbar"
                                style="width: {{ $timetable->progress }}%;" aria-valuenow="{{ $timetable->progress }}"
                                aria-valuemin="0" aria-valuemax="100">
                                {{ $timetable->progress }}%
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($timetable->file_url)
                        <div class="btn-group" role="group">
                            <a href="{{ URL::to('/timetables/viewv2/' . $timetable->id) }}"
                                class="btn btn-sm btn-info view-btn" data-id="{{ $timetable->id }}">
                                <span class="fa fa-eye"></span> VIEW
                            </a>
                            <a href="{{ URL::to('/timetables/print/' . $timetable->id) }}"
                                class="btn btn-sm btn-primary print-btn" data-id="{{ $timetable->id }}">
                                <span class="fa fa-print"></span> PRINT
                            </a>
                            @if (Auth::user()->lvl == 'dosen')
                            @else
                            <form action="{{ route('timetables.destroy', $timetable->id) }}" method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('Are you sure you want to delete this timetable?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger delete-btn"
                                    data-id="{{ $timetable->id }}">
                                    <span class="fa fa-trash"></span> DELETE
                                </button>
                            </form>
                            @endif

                        </div>
                        @else
                        N/A
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div id="pagination">
            {!! $timetables->render() !!}
        </div>
        @else
        <div class="no-data text-center">
            <p>Belum ada jadwal yang dibuat</p>
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Loop through each timetable and call updateProgress
        @foreach ($timetables as $timetable)
            updateProgress({{ $timetable->id }});
        @endforeach
    });

    function updateProgress(timetableId) {
        $.ajax({
            url: '/timetable/progress/' + timetableId,
            method: 'GET',
            success: function(data) {
                // Check if the status is ON PROGRESS before starting the interval
                if (data.status === 'IN PROGRESS') {
                    const intervalId = setInterval(function() {
                        $.ajax({
                            url: '/timetable/progress/' + timetableId,
                            method: 'GET',
                            success: function(data) {
                                // Update the progress bar
                                const progressBar = document.querySelector(`#progress-bar-${timetableId}`);
                                if (progressBar) {
                                    progressBar.style.width = data.progress + '%';
                                    progressBar.setAttribute('aria-valuenow', data.progress);
                                    progressBar.innerText = data.progress + '%';

                                    // Check if the status is COMPLETED
                                    if (data.status === 'COMPLETED') {
                                        setTimeout(function(){
                                        clearInterval(intervalId);
                                        location.reload();
                                        }, 2000);
                                       
                                    }

                                    // // Stop the interval if progress is 100%
                                    // if (data.progress >= 100) {
                                    //     clearInterval(intervalId);
                                       
                                    // }
                                }
                            }
                        });
                    }, 2000); // Update every 2 seconds
                }
            }
        });
    }
</script>