<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardService;

use App\Models\Day;
use App\Models\Timetable;
use App\Models\AcademicPeriod;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
    /**
     * Create a new instance of this controller
     *
     */
    public function __construct(DashboardService $service)
    {
        $this->service = $service;
        $this->middleware('auth');
        $this->middleware('activated');
    }

    /**
     * Show the application's dashboard
     */
    public function index()
    {
        $data = $this->service->getData();
        $timetables = Timetable::orderBy('created_at', 'DESC')->paginate(10);
        $days = Day::all();
        $academicPeriods = AcademicPeriod::all();
        return view('dashboard.index', compact('data', 'timetables', 'days', 'academicPeriods'));
    }

    public function search(Request $request)
{
    $query = Timetable::query();
    $days = Day::all();
    $academicPeriods = AcademicPeriod::all();

    // Check if there is a search query
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('status', 'LIKE', "%{$search}%");
        });
    }

    // Get the paginated results
    $timetables = $query->paginate(10);

    return view('dashboard.index', compact('timetables','days', 'academicPeriods'));
}

public function searchv2(Request $request)
{
    $query = Timetable::query();
$academicPeriods = AcademicPeriod::all();
$days = Day::all();

     if ($request->has('prodi') && $request->has('semester')) {
    $prodi = $request->input('prodi');
    $semester = $request->input('semester');

    $query->where(function($q) use ($prodi, $semester) {
        // Ensure both prodi and semester are included in the name
        $q->where('name', 'LIKE', "%{$prodi}%")
          ;
    });
        }
        $timetables = $query->paginate(10);

    return view('dashboard.index', compact('timetables','days', 'academicPeriods'));
}



public function indexv2(Request $request)
{
    $academicPeriods = AcademicPeriod::all();
    $days = Day::all();
    $searchTerm = $request->input('search'); // Get the search term from the request
    $timetables = []; // Initialize an array to hold timetable data

    // Path to the directory containing HTML files
    $directoryPath = storage_path('app\public\timetables');

    // Read all HTML files in the directory
    $files = File::files($directoryPath);

    foreach ($files as $file) {
        $content = File::get($file);
        $dom = new \DOMDocument();

        // Suppress errors due to malformed HTML
        libxml_use_internal_errors(true);
        $dom->loadHTML($content);
        libxml_clear_errors();

        // Extract majors and their timetables
        $majors = $dom->getElementsByTagName('h3');
        $period = $dom->getElementById('period');
         // Get the period text, if it exists
         $periodText = $period ? $period->textContent : ''; // Safely get the period text
        
        foreach ($majors as $major) {
            // Check if the major name matches the search term
            if (stripos($major->textContent, $searchTerm) !== false) {
                // Get the next sibling which contains the timetable
                $timetableHtml = '';
                $nextSibling = $major->nextSibling;

                // Loop through the siblings to find the timetable
                while ($nextSibling) {
                    // If we encounter a table, we capture its HTML
                    if ($nextSibling->nodeName === 'div' && $nextSibling->hasAttributes()) {
                        $timetableHtml = $nextSibling->ownerDocument->saveHTML($nextSibling);
                        break; // Exit the loop after finding the timetable
                    }
                    $nextSibling = $nextSibling->nextSibling; // Move to the next sibling
                }

                // Add to the timetable array
                $timetables[] = [
                    'name' => $major->textContent,
                    'period' => $periodText,
                    'html' => $timetableHtml,
                ];
            }
        }
    }

    return view('dashboard.index', compact('timetables', 'searchTerm','days', 'academicPeriods'));
}


public function show($id)
{
    // Retrieve the timetable by ID
    $timetable = Timetable::findOrFail($id);

    // Pass the timetable data to the view
    return view('dashboard.show', compact('timetable'));
}

}