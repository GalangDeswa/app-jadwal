<?php

namespace App\Services\GeneticAlgorithm;

use Storage;

use App\Models\Day as DayModel;
use App\Models\Room as RoomModel;
use App\Models\Course as CourseModel;
use App\Models\Timeslot as TimeslotModel;
use App\Models\CollegeClass as CollegeClassModel;
use App\Models\Professor as ProfessorModel;
use Illuminate\Support\Carbon;

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
        $timeslots = TimeslotModel::all();
        $classes = CollegeClassModel::all();
        $periodid = $this->timetable->academic_period_id;

        // Sort timeslots by time before rendering
        $timeslots = $timeslots->sortBy(function($timeslot) {
            return Carbon::createFromFormat('H:i - H:i', $timeslot->time);
        })->values(); // Use values() to reset the keys

        $tableTemplate = '<h3 class="text-center">{TITLE}</h3>
                         <h4 id="period" class="text-center">Semester - {period}</h4>
                         <div style="page-break-after: always">
                           <table class="table table-bordered"
                               style="max-width: 800px; width: 100%; font-size: 12px; margin: auto;">
                                <thead>
                                    <tr>
                                        <td><strong>HARI</strong></td>
                                        <td><strong>PUKUL</strong></td>
                                        <td><strong>RUANG</strong></td>
                                        <td><strong>MATA KULIAH</strong></td>
                                        <td><strong>NAMA DOSEN</strong></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    {BODY}
                                </tbody>
                            </table>
                        </div>';

        $content = "";

        foreach ($classes as $class) {
            $body = "";
            $hasCourses = false; // Flag to check if there are any courses

            // Iterate over each day
            foreach ($days as $day) {
                // Iterate over each sorted timeslot
                foreach ($timeslots as $timeslot) {
                    if (isset($data[$class->id][$day->name][$timeslot->time])) {
                        $slotData = $data[$class->id][$day->name][$timeslot->time];
                        $courseCode = $slotData['course_code'];
                        $courseName = $slotData['course_name'];
                        $professor = $slotData['professor'];
                        $room = $slotData['room'];

                        // Create a row for this course
                        $body .= "<tr>
                            <td id='hari'>" . strtoupper($day->name) . "</td>
                            <td id='waktu'>{$timeslot->time}</td>
                            <td id='ruang'>{$room}</td>
                            <td id='matkul'><strong>{$courseName}</strong></td>
                            <td id='dosen'>{$professor}</td>
                        </tr>";

                        $hasCourses = true; // Mark that there are courses for this class
                    }
                }
            }

            // Only include the class in the content if there are courses
            if ($hasCourses) {
                $title = $class->name;
                $content .= str_replace(['{TITLE}','{period}', '{BODY}'], [$title, $periodid, $body], $tableTemplate);
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