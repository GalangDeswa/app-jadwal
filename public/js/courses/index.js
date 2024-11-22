/**
 * An object for managing tasks related to courses
 */
function Course(url, resourceName) {
    Resource.call(this, url, resourceName);
}

App.extend(Resource, Course);

Course.prototype.prepareForUpdate = function (resource) {
  
    $('input[name=name]').val(resource.name);
    $('input[name=course_code]').val(resource.course_code);
    $('input[name=credit]').val(resource.credit)
    $('#type-select').val(resource.course_type).change();
    $('#professors-select').val(resource.professor_ids).change();
    
    // var $modal = $('#resource-modal');
    // if(resource != null){
    //      $modal.find('.modal-title').html('Edit');
    // }else{
    //     $modal.find('.modal-title').html('Tambah');
    // }
   
};

Course.prototype.openModalForEdit = function (resource) {
    this.prepareForUpdate(resource);

    // Update the modal header title
    $('#modal-heading').text('Edit kelas');

    // Update the button to reflect the edit action
    $('#modal-save-button').text('Update Course');

    // Show the modal
    $('#course-modal').modal('show');
};

window.addEventListener('load', function () {
    var course= new Course('/courses', 'Course');
    course.init();
});