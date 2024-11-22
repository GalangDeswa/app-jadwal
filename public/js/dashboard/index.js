/**
 * An object for managing tasks related to courses
 */
function Timetable(url, resourceName) {
    Resource.call(this, url, resourceName);
}





 function searchMajor() {
            const searchTerm = document.getElementById('majorSearch').value.toLowerCase();
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = ''; // Clear previous results

            // Get all major sections
            const majorSections = document.querySelectorAll('h3.text-center');

            let found = false; // Flag to track if any major is found

            majorSections.forEach(section => {
                const majorName = section.textContent.toLowerCase();
                if (majorName.includes(searchTerm)) {
                    found = true; // Set flag to true if a match is found
                    const timetable = section.nextElementSibling.outerHTML; // Get the timetable
                    resultDiv.innerHTML += `<h3>${section.textContent}</h3>${timetable}`; // Display the timetable
                }
            });

            if (!found) {
                resultDiv.innerHTML = '<p>No timetable found for the specified major.</p>';
            }
        }

App.extend(Resource, Timetable);

Timetable.prototype.init = function() {
    var self = this;
    Resource.prototype.init.call(self);

    $(document).on('click', '.print-btn', function(event) {
        var url = '/timetables/view/' + $(this).data('id');
        var printWin = window.open('', '', 'width=5,height=5');

        event.preventDefault();
        self.printTimetable(printWin, url);
    });
};

Timetable.prototype.printTimetable = function(printWin, url) {
    $.get(url, null, function (response) {
        printWin.resizeTo(window.innerWidth, window.innerHeight);
        printWin.document.open();
        printWin.document.write(response);
        printWin.document.close();

        // Wait for the page to load, and after that we print and close the window
        printWin.onload = function () {
            printWin.focus();
            printWin.print();
            printWin.close();
        };
    });
};

Timetable.prototype.initializeAddModal = function() {
    var $modal = $('#resource-modal');
    Resource.prototype.initializeAddModal.call(this);

    // Set up modal title and button label
    $modal.find('.modal-heading').html('Buat timetable baru');
    $modal.find('.submit-btn').html('Generate');
};


window.addEventListener('load', function () {
    var timetable = new Timetable('/timetables', 'Timetable');
    timetable.init();
    console.log(timetable.baseUrl);
});