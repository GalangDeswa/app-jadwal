<?php

namespace App\Services\GeneticAlgorithm;

use App\Events\TimetablesGenerated;

use App\Models\Course;
use App\Models\Room as RoomModel;
use App\Models\Timeslot as TimeslotModel;
use App\Models\Timetable as TimetableModel;
use App\Models\Professor as ProfessorModel;
use App\Models\CollegeClass as CollegeClassModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class TimetableGA
{
    /**
     * Timetable we want to run the algorithm for
     *
     * @var App\Models\Timetable
     */
    protected $timetable;

    /**
     * Create a new instance of TimetableGA class
     *
     * @param App\Models\Timetable $timetable Timetable we want to run the algorithm
     *                                        to generate
     */
    public function __construct(TimetableModel $timetable)
    {
        $this->timetable = $timetable;
    }

    /**
     * Create the problem instance for the algorithm
     *
     */
    public function initializeTimetable()
    {
        $maxContinuousSlots = 1;
        $timetable = new Timetable($maxContinuousSlots);

        // Set up rooms for the GA data using rooms data from DB
        $rooms = RoomModel::all();

        foreach ($rooms as $room) {
            $timetable->addRoom($room->id);
        }

        // Set up timeslots
        $days = $this->timetable->days;
        $timeslots = TimeslotModel::all();
        $count = 1;

        foreach ($days as $day) {
            foreach ($timeslots as $timeslot) {
                $timeslotId = 'D' . $day->id . "T" . $timeslot->id;
                $nextTimeslotId = $this->getNextTimeslotId($day, $timeslot);
                $timetable->addTimeslot($timeslotId, $nextTimeslotId);
            }
        }

        // Set up professors
        $professors = ProfessorModel::all();

        foreach ($professors as $professor) {
            $unavailableSlotIds = [];

            foreach ($professor->unavailable_timeslots as $timeslot) {
                $unavailableSlotIds[] = 'D' . $timeslot->day_id . 'T' . $timeslot->timeslot_id;
            }

            $timetable->addProfessor($professor->id, $unavailableSlotIds);
        }

        // Set up courses
        $results = DB::table('courses_classes')
            ->where('academic_period_id', $this->timetable->academic_period_id)
            ->selectRaw('distinct course_id')
            ->get();

        $semesterCourseIds = [];

        foreach ($results as $result) {
            $semesterCourseIds[] = $result->course_id;
        }

        $courses = Course::whereIn('id', $semesterCourseIds)->get();

        foreach ($courses as $course) {
            $professorIds  = [];

            foreach ($course->professors as $professor) {
                $professorIds[] = $professor->id;
            }

            $timetable->addModule($course->id, $professorIds);
        }

        // Set up class groups
        $classes = CollegeClassModel::all();

        foreach ($classes as $class) {
            $courseIds = [];
            $courses = $class->courses()->wherePivot('academic_period_id', $this->timetable->academic_period_id)->get();

            foreach ($courses as $course) {
                $courseIds[] = $course->id;
            }

            $timetable->addGroup($class->id, $courseIds);
        }


        return $timetable;
    }


    /**
     * Get the id of the next timeslot after the one given
     *
     */
    public function getNextTimeslotId($day, $timeslot)
    {
        $highestRank = TimeslotModel::count();
        $currentRank = (int)($timeslot->rank);
        $id = '';
        $endId = 'D0T0';

        if (($currentRank + 1) <= $highestRank) {
            $nextTimeslot = TimeslotModel::where('rank', ($currentRank + 1))->first();

            if ($nextTimeslot) {
                $id = 'D' . $day->id  . 'T' . $nextTimeslot->id;
            } else {
                $id = $endId;
            }
        } else {
            $id = $endId;
        }

        return $id;
    }


// public function getNextTimeslotId($day, $timeslot)
// {
//     $highestRank = TimeslotModel::count();
//     $currentRank = (int)($timeslot->rank);
//     $id = '';
//     $endId = 'D0T0';
//     $timeParts = explode(' - ', $timeslot->time);
//     $startTime = $timeParts[0]; // Get the start time
//     $endTime = $timeParts[1]; // Get the end time

//    // $endTime = Carbon::parse($timeslot->time)->format('H:i');


//     if (($currentRank + 1) <= $highestRank) {
        

//        // $nextTimeslots = TimeslotModel::where('rank', ($currentRank + 1))->where('time', '>=', $endTime)->get();
//         $nextTimeslots = TimeslotModel::whereRaw("SUBSTRING_INDEX(time, ' - ', 2) > ?", [$endTime])
//         ->orderByRaw("SUBSTRING_INDEX(time, ' - ', 1) ASC") // Order by start time
//         ->get();

//         // // Fetch the next timeslot with the next rank
//         // $nextTimeslots = TimeslotModel::where('rank', $nextRank)
//         // ->whereRaw("SUBSTRING_INDEX(time, ' - ', 1) > ?", [$endTime])
//         // ->first();

//         // // If no suitable timeslot is found at the next rank, you can continue to search for the next ranks
//         // if (!$nextTimeslot) {
//         // $nextTimeslot = TimeslotModel::where('rank', '>', $currentRank)
//         // ->whereRaw("SUBSTRING_INDEX(time, ' - ', 1) > ?", [$endTime])
//         // ->orderBy('rank', 'asc') // Order by rank to find the earliest valid timeslot
//         // ->first();
//         // }

//         if ($nextTimeslots->count() > 0) {
//             $nextTimeslot = $nextTimeslots->first();
//             $id = 'D' . $day->id  . 'T' . $nextTimeslot->id;
//             echo " next timeslot id --------->   ". $nextTimeslot->time."\n";
//         } else {
//             $id = $endId;
//         }
//     } else {
//         $id = $endId;
//     }

//     return $id;
// }

    /**
     * Run the timetable generation algorithm
     *
     */
    public function run()
    {
        try {
            $maxGenerations = 150;

            $timetable = $this->initializeTimetable();

          // $algorithm = new GeneticAlgorithm(150, 0.01, 0.9, 2, 10);
          // $algorithm = new GeneticAlgorithm(200, 0.05, 0.8, 5, 10);

            // v2
        $algorithm = new GeneticAlgorithm(250, 0.03, 0.8, 7, 10);
            //v3
      // $algorithm = new GeneticAlgorithm(200, 0.05, 0.9, 5, 5);
            //v4
         //$algorithm = new GeneticAlgorithm(100, 0.09, 0.9, 2, 10);

            $population = $algorithm->initPopulation($timetable);

            $algorithm->evaluatePopulation($population, $timetable);

          
            // Keep track of current generation
            $generation = 1;

            while (
                !$algorithm->isTerminationConditionMet($population)
                && !$algorithm->isGenerationsMaxedOut($generation, $maxGenerations)
            ) {
                $fittest = $population->getFittest(0);
                print "Generation: " . $generation . "( " . number_format($fittest->getFitness(), 4) . ") - ";
                print $fittest;
                print "\n";
                echo "-----------------generation------------------------\n";

                // Apply crossover
                $population = $algorithm->crossoverPopulation($population);

                // Apply mutation
                $population = $algorithm->mutatePopulation($population, $timetable);

                // Evaluate Population
                $algorithm->evaluatePopulation($population, $timetable);

                // Increment current
                $generation++;

                // Cool temperature of GA for simulated annealing
                $algorithm->coolTemperature();


                 // Calculate progress
                 $progress = ($generation / $maxGenerations) * 100;
                 $this->timetable->update(['progress' => $progress]);
            }

            $solution =  $population->getFittest(0);
            $scheme = $timetable->getScheme();
            $timetable->createClasses($solution);
            $classes = $timetable->getClasses();

            // Update the timetable data in the DB
            $this->timetable->update([
                'chromosome' => $solution->getChromosomeString(),
                'fitness' => $solution->getFitness(),
                'generations' => $generation,
                'scheme' => $scheme,
                'status' => 'COMPLETED'
            ]);

            // Save scheduled classes' information for professors
            foreach ($classes as $class) {
                $groupId = $class->getGroupId();
                $timeslot = $timetable->getTimeslot($class->getTimeslotId());
                $dayId = $timeslot->getDayId();
                $timeslotId = $timeslot->getTimeslotId();
                $professorId = $class->getProfessorId();
                $moduleId = $class->getModuleId();
                $roomId = $class->getRoomId();

                $this->timetable->schedules()->create([
                    'day_id' => $dayId,
                    'timeslot_id' => $timeslotId,
                    'professor_id' => $professorId,
                    'course_id' => $moduleId,
                    'class_id' => $groupId,
                    'room_id' => $roomId
                ]);
            }
            

            event(new TimetablesGenerated($this->timetable));

            $this->timetable->update(['progress' => 100]);

        } catch (\Throwable $th) {
            print $th->getMessage()." ".$th->getLine()." ".$th->getFile();
            Log::error("Error while generating timetable " . $th->getMessage(), ['trace' => $th->getTrace()]);
        }
    }
}