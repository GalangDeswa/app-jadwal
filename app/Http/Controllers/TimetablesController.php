<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TimetableService;
use App\Events\TimetablesRequested;

use App\Models\Day;
use App\Models\Timetable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TimetablesController extends Controller
{

    protected TimetableService $service;

    /**
     * Create a new instance of this controller and set up
     * middlewares on this controller methods
     */
    public function __construct(TimetableService $service)
    {
        $this->service = $service;
        $this->middleware('auth');
        $this->middleware('activated');
    }

    /**
     * Handle ajax request to load timetable to populate
     * timetables table on dashboard
     *
     */
    public function index()
    {
        $timetables = Timetable::orderBy('created_at', 'DESC')->paginate(10);

        return view('dashboard.timetables', compact('timetables'));
    }

    /**
     * Create a new timetable object and hand over to genetic algorithm
     * to generate
     *
     * @param \Illuminate\Http\Request $request The HTTP request
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'academic_period_id' => 'required'
        ];

        $messages = [
            'academic_period_id.required' => 'An academic period must be selected'
        ];

        $this->validate($request, $rules, $messages);

        $errors = [];
        $dayIds = [];

        $days = Day::all();

        foreach ($days as $day) {
            if ($request->has('day_' . $day->id)) {
                $dayIds[] = $day->id;
            }
        }

        if (!count($dayIds)) {
            $errors[] = 'At least one day should be selected';
        }

        if (count($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        $otherChecks = $this->service->checkCreationConditions();

        if (count($otherChecks)) {
            return response()->json(['errors' => $otherChecks], 422);
        }

        $timetable = Timetable::create([
            'user_id' => Auth::user()->id,
            'academic_period_id' => $request->academic_period_id,
            'status' => 'IN PROGRESS',
            'name' => $request->name
        ]);

        if ($timetable) {
            $timetable->days()->sync($dayIds);
        }

        event(new TimetablesRequested($timetable));

        return response()->json(['message' => 'Jadwal sedang diproses'], 200);
    }

    /**
     * Display a printable view of timetable set
     *
     * @param int $id
     */
    public function view($id)
    {
        $timetable = Timetable::find($id);

        if (!$timetable) {
            return redirect('/');
        } else {
            $path = $timetable->file_url;
            $timetableData =  Storage::get($path);
            $timetableName = $timetable->name;
            return view('timetables.view', compact('timetableData', 'timetableName'));
        }
    }


     public function viewv2($id)
    {
        $timetable = Timetable::find($id);

        if (!$timetable) {
            return redirect('/');
        } else {
            $path = $timetable->file_url;
            $timetableData =  Storage::get($path);
            $timetableName = $timetable->name;
            $timetableId = $timetable;
            return view('dashboard.show', compact('timetableData', 'timetableName','timetableId'));
        }
    }


//     public function view($id)
// {
//     $timetable = Timetable::find($id);

//     if (!$timetable) {
//         return redirect('/');
//     } else {
//         $path = $timetable->file_url; // Get the file path
//         $timetableData = Storage::get($path); // Read the HTML content
//         $timetableName = $timetable->name;

//         // Prepend CSS styles to the HTML content
//         $cssLinks = '
//         <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
//         <style>
//             body {
//                 background-color: #f8f9fa;
//                 font-family: Arial, sans-serif;
//             }
//             /* Add any additional custom styles here */
//             table {
//                 width: 100%;
//                 border-collapse: collapse;
//             }
//             th, td {
//                 padding: 10px;
//                 text-align: left;
//                 border: 1px solid #ddd;
//             }
//             th {
//                 background-color: #007bff;
//                 color: white;
//             }
//         </style>';

//         // Combine CSS with the timetable data
//         $fullHtml = $cssLinks . $timetableData;

//         return view('dashboard.show', compact('fullHtml', 'timetableName'));
//     }
// }


public function saveHtml(Request $request, $id)
{
    $request->validate([
        'content' => 'required|string',
    ]);

    // Define the file path (you can customize the path and filename)
    $filePath = 'public/timetables/timetable_' . $id . '.html';

    // Save the content to an HTML file
    Storage::disk('local')->put($filePath, $request->input('content'));

    return response()->json(['success' => true]);
}

public function destroy($id)
{
    // Find the timetable by ID
    $timetable = Timetable::findOrFail($id);

    // Construct the file path using the timetable ID
    $filePath = 'public/timetables/timetable_' . $timetable->id . '.html';

    // Delete the timetable
    $timetable->delete();

    // Delete the file from storage if it exists
    if (Storage::disk('local')->exists($filePath)) {
        Storage::disk('local')->delete($filePath);
    }

    // Redirect back with a success message
    return redirect('/dashboard')->with('success', 'Timetable deleted successfully.');
}
    public function getProgress($id)
{
    $timetable = Timetable::find($id);
    return response()->json(['progress' => $timetable->progress,
'status' => $timetable->status]);
}

}