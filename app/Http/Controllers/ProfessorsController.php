<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;
use App\Services\ProfessorsService;

use App\Models\Day;
use App\Models\Course;
use App\Models\Timeslot;
use App\Models\Professor;
use App\Models\UnavailableTimeslot;

class ProfessorsController extends Controller
{
    /**
     * Service class for handling operations relating to this
     * controller
     *
     * @var App\Services\ProfessorsService $service
     */
    protected $service;

    /**
     * Create a new instance of this controller
     *
     * @param App\Services\ProfessorsService $service This controller's service class
     */
    public function __construct(ProfessorsService $service)
    {
        $this->service = $service;
        $this->middleware('auth');
        $this->middleware('activated');
    }

    /**
     * Show landing page for professors module
     *
     * @param Illuminate\Http\Request $request The HTTP request
     */
    public function index(Request $request)
    {
        $professors = $this->service->all([
            'keyword' => $request->has('keyword') ? $request->keyword : null,
            'order_by' => 'name',
            'paginate' => 'true',
            'per_page' => 20
        ]);

        $courses = Course::all();
        $days = Day::all();
        $timeslots = Timeslot::all();

        if ($request->ajax()) {
            return view('professors.table', compact('professors'));
        }

        return view('professors.index', compact('professors', 'courses', 'days', 'timeslots'));
    }

    /**
     * Add a new professor to the database
     *
     * @param Illuminate\Http\Request The HTTP request
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|max:30',
            'email' => 'required|email|max:30',
        ];

        if ($request->has('email') && $request->email) {
            $rules['email'] = 'email';
        }

         $messages = [
         'name.max' => 'maximal karakter nama 30.',
         'email.max'=>'maximal karakter email 30'
        
         ];

        $this->validate($request, $rules,$messages);

        $professor = $this->service->store($request->all());

        if ($professor) {
            return response()->json(['message' => 'dosen ditambah'], 200);
        } else {
            return response()->json(['error' => 'An unknown system error occurred'], 500);
        }
    }

    /**
     * Get and return data about a professor
     *
     * @param int $id Id of professor
     * @return Illuminate\Http\Response The data as a JSON response
     */
    public function show($id)
    {
        $professor = $this->service->show($id);

        if ($professor) {
            return response()->json($professor, 200);
        } else {
            return response()->json(['errors' => ['tidak ditemukan']], 404);
        }
    }

    /**
     * Update the professor with the given id
     *
     * @param int $id Id of the professor
     * @param Illuminate\Http\Request $request The HTTP request
     */
    public function update($id, Request $request)
    {
        $professor = Professor::find($id);

        if (!$professor) {
            return response()->json(['errors' => ['tidak ditemukan']], 404);
        }

        $rules = [
            'name' => 'required|max:30',
             'email' => 'required|email|max:30',
        ];
        

        if ($request->has('email') && $request->email) {
            $rules['email'] = 'email';
        }

         $messages = [
         'name.max' => 'maximal karakter nama 30.',
         'email.max'=>'maximal karakter email 30'

         ];

        $this->validate($request, $rules,$messages);

        $professor = $this->service->update($id, $request->all());

        return response()->json(['message' => 'dosen diupdate'], 200);
    }


    /**
     * Delete the professor with the given id
     *
     * @param int $id Id of professor to delete
     */
    public function destroy($id)
    {
        $professor = Professor::find($id);

        if (!$professor) {
            return response()->json(['error' => 'tidak ditemukan'], 404);
        }

        if ($this->service->delete($id)) {
            return response()->json(['message' => 'dosen dihapus'], 200);
        } else {
            return response()->json(['error' => 'An unknown system error occurred'], 500);
        }
    }
}