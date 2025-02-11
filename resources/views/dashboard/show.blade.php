@extends('layouts.app')

@section('content')

<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 page-container">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title flex-grow-1">{{ $timetableName }}</h1>
        @if (Auth::user()->lvl != 'dosen')
        <button id="editButton" class="btn btn-primary">Edit</button>
        @endif
    </div>

    <div style="margin: 15px 0;">
        <!-- Adjust the margin values as needed -->
        <input type="text" id="searchInput" placeholder="cari jadwal" class="form-control"
            style="display: inline-block; width: auto;">
        <button id="searchButton" class="btn btn-secondary">Search</button>
    </div>

    <div id="timetableContainer">
        {!! $timetableData !!}
    </div>
    <div id="addRowContainer" style="display: none;">
        <button id="addRowButton" class="btn btn-secondary">Add Row</button> <!-- Hidden by default -->
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery -->
<script>
    // Function to check for time overlaps and color them accordingly
    function checkTimeOverlaps() {
        const timetableRows = document.querySelectorAll('#timetableContainer table tr');
        const defaultColor = 'lightgrey'; // Default color for non-overlapping rows

        // Clear previous highlights
        timetableRows.forEach(row => {
            row.style.backgroundColor = ''; // Reset color
        });

        // Iterate through each row
        for (let i = 0; i < timetableRows.length; i++) {
            const currentRow = timetableRows[i];
            const dayCell = currentRow.querySelector('td[id="hari"]');
            const timeCell = currentRow.querySelector('td[id="waktu"]');

            if (dayCell && timeCell) {
                const currentDay = dayCell.textContent.trim();
                const currentTimeRange = timeCell.textContent.trim();

                // Parse the time range (assuming format is "HH:MM - HH:MM")
                const timeParts = currentTimeRange.split(' - ');
                if (timeParts.length === 2) {
                    const currentStartTime = new Date(`1970-01-01T${timeParts[0]}:00`);
                    const currentEndTime = new Date(`1970-01-01T${timeParts[1]}:00`);

                    // Check the next row for overlaps
                    if (i + 1 < timetableRows.length) {
                        const nextRow = timetableRows[i + 1];
                        const nextDayCell = nextRow.querySelector('td[id="hari"]');
                        const nextTimeCell = nextRow.querySelector('td[id="waktu"]');

                        if (nextDayCell && nextTimeCell) {
                            const nextDay = nextDayCell.textContent.trim();
                            const nextTimeRange = nextTimeCell.textContent.trim();

                            // Parse the next time range
                            const nextTimeParts = nextTimeRange.split(' - ');
                            if (nextTimeParts.length === 2) {
                                const nextStartTime = new Date(`1970-01-01T${nextTimeParts[0]}:00`);
                                const nextEndTime = new Date(`1970-01-01T${nextTimeParts[1]}:00`);

                                // Check if the days are the same and if the times overlap
                                if (currentDay === nextDay && (currentStartTime < nextEndTime && currentEndTime > nextStartTime)) {
                                    // Highlight both rows in red
                                    currentRow.style.backgroundColor = 'red';
                                    nextRow.style.backgroundColor = 'red';
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    document.getElementById('editButton')?.addEventListener('click', function() {
        const timetableContainer = document.getElementById('timetableContainer');
        const addRowContainer = document.getElementById('addRowContainer');
        
        // Check if we are currently in edit mode
        if (timetableContainer.isContentEditable) {
            // If it is editable, turn off editing
            timetableContainer.contentEditable = "false";
            this.textContent = "Edit";
            timetableContainer.style.border = "none"; // Remove any border indication
            addRowContainer.style.display = "none"; // Hide the add row button
            
            // Save the changes via AJAX
            const updatedContent = timetableContainer.innerHTML;
            $.ajax({
                url: '{{ route("timetable.saveHtml", $timetableId) }}', // Adjust the route as necessary
                method: 'POST',
                data: {
                    content: updatedContent,
                    _token: '{{ csrf_token() }}' // Include CSRF token for security
                },
                success: function(response) {
                    alert('Timetable updated successfully!');
                    checkTimeOverlaps(); // Check for overlaps after saving
                },
                error: function(xhr) {
                    alert('An error occurred while saving the timetable.');
                }
            });
        } else {
            // If it is not editable, turn on editing
            timetableContainer.contentEditable = "true";
            this.textContent = "Save";
            timetableContainer.style.border = "1px solid #ccc"; // Add a border to indicate editing
            timetableContainer.focus(); // Focus on the timetable for editing
            addRowContainer.style.display = "block"; // Show the add row button
        }
    });

    document.getElementById('addRowButton').addEventListener('click', function() {
        const timetableContainer = document.getElementById('timetableContainer');
        
        // Get the selected row
        const selectedRow = document.querySelector('.selected-row');
        if (selectedRow) {
            const table = selectedRow.closest('table'); // Find the closest table to the selected row
            const newRow = table.insertRow(selectedRow.rowIndex + 1); // Insert a new row directly below the selected row
            const numberOfColumns = selectedRow.cells.length; // Get the number of columns from the selected row
            
            // Create new cells in the new row
            for (let i = 0; i < numberOfColumns; i++) {
                const newCell = newRow.insertCell(i); // Create a new cell in the new row
                newCell.textContent = "-"; // Set the cell content to "-"
                newCell.contentEditable = "true"; // Make the cell editable
            }
            newRow.scrollIntoView(); // Scroll to the new row

            // Check for overlaps after adding a new row
            checkTimeOverlaps();
        } else {
            alert("Please select a row to add a new row below it.");
        }
    });

    // Search functionality
    document.getElementById('searchButton').addEventListener('click', function() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const timetableRows = document.querySelectorAll('#timetableContainer table tr');

        timetableRows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            let rowContainsSearchTerm = false;

            // Check each cell in the row for the search term
            for (let i = 0; i < cells.length; i++) {
                if (cells[i].textContent.toLowerCase().includes(searchInput)) {
                    rowContainsSearchTerm = true;
                    break;
                }
            }

            // Show or hide the row based on whether it contains the search term
            if (rowContainsSearchTerm) {
                row.style.display = ''; // Show the row
            } else {
                row.style.display = 'none'; // Hide the row
            }
        });
    });

    // Call checkTimeOverlaps on page load to check existing rows
    document.addEventListener('DOMContentLoaded', checkTimeOverlaps);
</script>

@endsection