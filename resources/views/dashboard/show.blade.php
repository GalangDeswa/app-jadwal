@extends('layouts.app')

@section('content')

<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 page-container">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title flex-grow-1">{{ $timetableName }}</h1>
        <button id="editButton" class="btn btn-primary">Edit</button>
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
    document.getElementById('editButton').addEventListener('click', function() {
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
        } else {
            alert("Please select a row to add a new row below it.");
        }
    });

    // Add click event to each row to select it
    document.querySelectorAll('#timetableContainer table tr').forEach(row => {
        row.addEventListener('click', function() {
            // Remove 'selected-row' class from any previously selected row
            document.querySelectorAll('.selected-row').forEach(selected => {
                selected.classList.remove('selected-row');
            });
            // Add 'selected-row' class to the clicked row
            this.classList.add('selected-row');
        });
    });
</script>

@endsection