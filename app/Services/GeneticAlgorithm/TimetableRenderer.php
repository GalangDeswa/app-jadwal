<?php

namespace App\Services\GeneticAlgorithm;

use Storage;

use App\Models\Day as DayModel;
use App\Models\Room as RoomModel;
use App\Models\Course as CourseModel;
use App\Models\Timeslot as TimeslotModel;
use App\Models\CollegeClass as CollegeClassModel;
use App\Models\Professor as ProfessorModel;

class TimetableRenderer
{
    /**
     * Create a new instance of this class
     *
     * @param App\Models\Timetable Timetable whose data we are rendering
     */

     protected $timetable;

    public function __construct($timetable)
    {
        $this->timetable = $timetable;
    }

    /**
     * Generate HTML layout files out of the timetable data
     *
     * Chromosome interpretation is as follows
     * Timeslot, Room, Professor
     *
     */
   public function render()
{
    try {
        $chromosome = explode(",", $this->timetable->chromosome);
        $scheme = explode(",", $this->timetable->scheme);
        $data = $this->generateData($chromosome, $scheme);

        $days = $this->timetable->days()->orderBy('id', 'ASC')->get();
        $timeslots = TimeslotModel::orderBy('rank', 'ASC')->get();
        $classes = CollegeClassModel::all();
        $periodid = $this->timetable->academic_period_id;

        $tableTemplate = '<h3 class="text-center">{TITLE}</h3>
                         <h4 id="period" class="text-center">Semester - {period}</h4>
                         <div style="page-break-after: always">
                           <table class="table table-bordered"
                               style="max-width: 800px; width: 100%; font-size: 12px; margin: auto;">
                                <thead>
                                    {HEADING}
                                </thead>
                                <tbody>
                                    {BODY}
                                </tbody>
                            </table>
                        </div>';

        $content = "";

        foreach ($classes as $class) {
            $header = "<tr class='table-head'>";
            $header .= "<td><strong>Waktu</strong></td>";

            // Create header for each day
            foreach ($days as $day) {
                $header .= "<td><strong>" . strtoupper($day->name) . "</strong></td>";
            }
            $header .= "</tr>";

            $body = "";
            $hasCourses = false; // Flag to check if there are any courses

            // Iterate over each timeslot
            foreach ($timeslots as $timeslot) {
                $rowContent = "<tr>
                    <td style='padding: 5px;'>" . $timeslot->time . "</td>"; // Show the timeslot in the first column
                $rowHasCourse = false; // Flag to check if this row has any course

                // Check for courses for each day in this timeslot
                foreach ($days as $day) {
                    if (isset($data[$class->id][$day->name][$timeslot->time])) {
                        $slotData = $data[$class->id][$day->name][$timeslot->time];
                        $courseCode = $slotData['course_code'];
                        $courseName = $slotData['course_name'];
                        $professor = $slotData['professor'];
                        $room = $slotData['room'];

                        $rowContent .= "<td class='text-center'>";
                       // $rowContent .= "<span class='course_code'>{$day->name}</span><br />";
                        $rowContent .= "<span class='course_name'>{$courseName}</span><br />";
                        $rowContent .= "<span class='room pull-left'>Ruang {$room}</span> <hr>";
                        $rowContent .= "<span class='professor pull-right'>{$professor}</span> <hr>";
                        $rowContent .= "</td>";

                        $rowHasCourse = true; // Mark that this row has a course
                    } else {
                        // No course for this timeslot and day
                        $rowContent .= "<td class='text-center'><strong> - </strong></td>";
                    }
                }
                $rowContent .= "</tr>";

                // Only add this row to the body if it has at least one course
                if ($rowHasCourse) {
                    $body .= $rowContent;
                    $hasCourses = true; // Mark that there are courses for this class
                }
            }

            // Only include the class in the content if there are courses
            if ($hasCourses) {
                $title = $class->name;
                $content .= str_replace(['{TITLE}','{period}' ,'{HEADING}', '{BODY}'], [$title,$periodid,$header,
                $body], $tableTemplate);
            }
        }

        $path = 'public/timetables/timetable_' . $this->timetable->id . '.html';
        Storage::put($path, $content);

        $this->timetable->update([
            'file_url' => $path
        ]);
    } catch (\Throwable $th) {
        echo $th->getMessage() . "\n";
        echo $th->getLine() . "\n";
        echo $th->getFile();
    }
}

    /**
     * Get an associative array with data for constructing timetable
     *
     * @param array $chromosome Timetable chromosome
     * @param array $scheme Mapping for reading chromosome
     * @return array Timetable data
     */
    public function generateData($chromosome, $scheme)
    {
        $data = [];
        $schemeIndex = 0;
        $chromosomeIndex = 0;
        $groupId = null;

        while ($chromosomeIndex < count($chromosome)) {
            while ($scheme[$schemeIndex][0] == 'G') {
                $groupId = substr($scheme[$schemeIndex], 1);
                $schemeIndex += 1;
            }

            $courseId = $scheme[$schemeIndex];

            $class = CollegeClassModel::find($groupId);
            $course = CourseModel::find($courseId);

            $timeslotGene = $chromosome[$chromosomeIndex];
            $roomGene = $chromosome[$chromosomeIndex + 1];
            $professorGene = $chromosome[$chromosomeIndex + 2];

            // echo "-------------- bentuk gene-------------------"."\n";
            // echo "Timeslot Gene: " . $timeslotGene . "\n";
            // echo "room Gene: " . $roomGene . "\n";
            // echo "dosen gene: " . $professorGene . "\n";

            $matches = [];
            preg_match('/D(\d*)T(\d*)/', $timeslotGene, $matches);

            $dayId = $matches[1];
            $timeslotId = $matches[2];

            $day = DayModel::find($dayId);
            $timeslot = TimeslotModel::find($timeslotId);
            $professor = ProfessorModel::find($professorGene);
            $room = RoomModel::find($roomGene);

            if (!isset($data[$groupId])) {
                $data[$groupId] = [];
            }

            if (!isset($data[$groupId][$day->name])) {
                $data[$groupId][$day->name] = [];
            }

            if (!isset($data[$groupId][$day->name][$timeslot->time])) {
                $data[$groupId][$day->name][$timeslot->time] = [];
            }

            $data[$groupId][$day->name][$timeslot->time]['course_code'] = $course->course_code;
            $data[$groupId][$day->name][$timeslot->time]['course_name'] = $course->name;
            $data[$groupId][$day->name][$timeslot->time]['room'] = $room->name;
            $data[$groupId][$day->name][$timeslot->time]['professor'] = $professor->name;

            $schemeIndex++;
            $chromosomeIndex += 3;
        }

        return $data;
    }
}