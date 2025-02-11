<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TimetablesController;
use App\Http\Controllers\QueueController;

Route::get('/', function() {
    return redirect('/dashboard');
});


// Route::post('/start-queue-listener', [App\Http\Controllers\QueueController::class,
// 'startListener'])->name('queue.start');


// Route::post('/start-queue', [QueueController::class, 'startQueue'])->name('start.queue');


// Route::get('/qstart', function () {

// chdir('..');

// exec('cache:clear');

// });



Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
Route::get('/search', 'DashboardController@search')->name('search');
Route::get('/searchv2', 'DashboardController@searchv2')->name('searchv2');

Route::get('/dashboardsiswa', [DashboardController::class, 'indexv2'])->name('timetables.index');
// Routes for rooms module
Route::resource('rooms', 'RoomsController');

// Routes for courses module
Route::resource('courses', 'CoursesController');

// Routes for timeslots module
Route::resource('timeslots', 'TimeslotsController');

// Routes for professors module
Route::resource('professors', 'ProfessorsController');

// Routes for college classes
Route::resource('classes', 'CollegeClassesController');

// Routes for timetable generation
Route::post('timetables', 'TimetablesController@store');
Route::get('timetables', 'TimetablesController@index');
Route::get('timetables/view/{id}', 'TimetablesController@view');
Route::get('timetables/viewv2/{id}', 'TimetablesController@viewv2');


Route::post('/timetable/save-html/{id}', [TimetablesController::class, 'saveHtml'])->name('timetable.saveHtml');

Route::delete('/timetables/delete/{id}', [TimetablesController::class, 'destroy'])->name('timetables.destroy');


Route::get('/timetable/progress/{id}', [TimetablesController::class, 'getProgress'])->name('progress');

// User account activation routes
Route::get('/users/activate', 'UsersController@showActivationPage');
Route::post('/users/activate', 'UsersController@activateUser');

Route::get('/home', 'HomeController@index')->name('home');

// Other account related routes
Route::get('/login', 'UsersController@showLoginPage');
Route::get('/register', 'UsersController@showRegisterPage')->name('register');
Route::post('/registerpost', 'UsersController@store')->name('registerpost');
Route::post('/login', 'UsersController@loginUser');
Route::get('/request_reset', 'UsersController@showPasswordRequestPage');
Route::post('/request_reset', 'UsersController@requestPassword');
Route::get('/reset_password', 'UsersController@showResetPassword');
Route::post('/reset_password', 'UsersController@resetPassword');
Route::get('/my_account', 'UsersController@showAccountPage');
Route::post('/my_account', 'UsersController@updateAccount');
Route::get('/logout', function() {
    Auth::logout();
    return redirect('/');
});