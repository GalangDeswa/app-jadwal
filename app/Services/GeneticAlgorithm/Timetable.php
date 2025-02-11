<?php

namespace App\Services\GeneticAlgorithm;

use App\Models\Day;
use Illuminate\Support\Facades\DB;

class Timetable
{
    /**
     * Rooms indexed by their IDs githubb
     *
     * @var array
     */
    private $rooms;

    /**
     * Collection of professors indexed by their IDs
     *
     * @var array
     */
    private $professors;

    /**
     * Collection of modules indexed by their IDs
     *
     * @var array
     */
    private $modules;

    /**
     * Collection of class groups indexed by their IDs
     *
     * @var array
     */
    private $groups;

    /**
     * Collection of time slots
     *
     * @var array
     */
    private $timeslots;

    /**
     * Available classes
     *
     * @var array
     */
    public array $classes;

    /**
     * Number of classes scheduled
     *
     * @var int
     */
    private $numClasses;

    /**
     * Maximum slots students can have continuously
     *
     * @var int
     */
    public $maxContinuousSlots;
        // New: Add working hours constraints
        private $workingHoursStart = 480; // 8:00 AM in minutes
        private $workingHoursEnd = 1020; // 5:00 PM in minutes

    /**
     * Create a new instance of this class
     */
    public function __construct($maxContinuousSlots)
    {
        $this->rooms = [];
        $this->professors = [];
        $this->modules = [];
        $this->groups = [];
        $this->timeslots = [];
        $this->numClasses = 0;
        $this->maxContinuousSlots = $maxContinuousSlots;
    }


       private $roomsByType = []; // Rooms organized by type

     

       public function findSuitableStartTime($credits) 
{
    $duration = $credits * 60; // Convert credits to minutes (1 credit = 1 hour = 60 minutes)
    $startTime = 480; // 8:00 AM in minutes
    $endTime = $startTime + $duration;
    $day = 1; // Start from the first day (Monday)

    while ($endTime > 1020) { // 1020 minutes is 5:00 PM
        $startTime = $this->getNextDay($startTime);
        $day++; // Move to the next day
        if ($day > 6) {
            // If we exceed 6 working days, reset to the first day
            $day = 1;
        }
        $endTime = $startTime + $duration;
    }

    //  echo "starttime--------------------------"."\n";
    //  echo $startTime."\n";
    //   echo "endtime--------------------------"."\n";
    //   echo $endTime."\n";
    // Calculate the time slot index (60-minute intervals)
    $timeSlotIndex = ($startTime - 480) / 60; // 480 is 8:00 AM
    // echo "time and day--------------------------"."\n";
    // echo "D{$day}T" . ($timeSlotIndex + 1)."\n";

$readableStartTime = $this->convertMinutesToTime($startTime);
$readableEndTime = $this->convertMinutesToTime($endTime);
$time = $readableStartTime."-".$readableEndTime;
$id = 1;
$idtime = $id++;

// echo 'readable time----------------------->>'."\n";
// echo $readableStartTime."\n";
// echo $readableEndTime."\n";
// echo $time."\n";

   // $add = timeslotv3::addTimeSlot($time);

   // $allTimeSlots = Timeslotv3::getAllTimeSlots();

    // Output the array of time slots
  //  print_r($allTimeSlots);

    //return $this->timeslots[$timeslotId] = new Timeslot($timeslotId, $next);
    //return "D{$day}T" . ($timeSlotIndex + 1); // Adding 1 to make it 1-indexed
}

   private function getNextDay($time) 
    {
        return $this->workingHoursStart + (ceil(($time - $this->workingHoursStart) / 1440) * 1440);
    }

    //  public function getRandomTimeslot()
    // {
    //     return $this->possibleStartTimes[array_rand($this->possibleStartTimes)];
    // }

    private function convertMinutesToTime($minutes)
{
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    
    // Format hours and minutes to always show two digits
    return sprintf('%02d:%02d', $hours, $mins);
}



    /**
     * Get the groups
     *
     * @return array The groups
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Get the timeslots
     *
     * @return array The timeslots
     */
    public function getTimeslots()
    {
        return $this->timeslots;
    }

    /**
     * Get the modules
     *
     * @return array The modules
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Get the professors
     *
     * @return array Collection of professors
     */
    public function getProfessors()
    {
        return $this->professors;
    }

    /**
     * Add a new lecture room
     *
     * @param int $roomId ID of room
     */
    public function addRoom($roomId)
    {
        // $this->rooms[$roomId] = new Room($roomId);

         $room = new Room($roomId);
         $this->rooms[$roomId] = $room;

         // Organize rooms by type
         if (!isset($this->roomsByType[$room->getRoomType()]))
         {
             $this->roomsByType[$room->getRoomType()] = [];
         }
         $this->roomsByType[$room->getRoomType()][] = $room;
    }

     // Get random room of specific type
     public function getRandomRoomByType($roomType)
     {
        if (isset($this->roomsByType[$roomType]) && !empty($this->roomsByType[$roomType]))
        {
            $typeRooms = $this->roomsByType[$roomType];
            return $typeRooms[array_rand($typeRooms)];
        }
     return null;
     }

     // Modified version to get appropriate room for a module
     public function getRandomRoomForModule($moduleId)
     {
        $module = $this->getModule($moduleId);
        $requiredType = $module->getRequiredRoomType();

        return $this->getRandomRoomByType($requiredType) ?? $this->getRandomRoom();
     }

      /**
     * Get a random timeslot by course credit
     *
     * @param int $courseCredits The credits of the course
     * @return Timeslot|null A random timeslot that matches the course credits or a random timeslot if none found
     */
    public function getRandomTimeslotByCourseCredit($courseCredits)
    {
        // Filter timeslots that match the required course credits
        $matchingTimeslots = array_filter($this->timeslots, function($timeslot) use ($courseCredits) {
           // echo "time credit match! --->  "."\n";
            return (int)$timeslot->getCredit() === $courseCredits;
        });

        // If there are matching timeslots, return a random one
        if (!empty($matchingTimeslots)) {
        
            return $matchingTimeslots[array_rand($matchingTimeslots)];
        }

        // If no matching timeslot is found, return a random timeslot from all available timeslots
        if (!empty($this->timeslots)) {
            // echo "noo credit match! ---> "."\n";
            return $this->timeslots[array_rand($this->timeslots)];
        }

        // Return null if no timeslots are available
        return null;
    }

    /**
     * Add a professor
     *
     * @param int $professorId Id of professor
     * @param string $unavailableSlots Slots that the professor can't teach
     */
    public function addProfessor($professorId, $unavailableSlots)
    {
        $this->professors[$professorId] = new Professor($professorId, $unavailableSlots);
    }

    /**
     * Add a new module
     *
     * @param int $moduleId Id of module
     * @param array $professorIds Ids of professors
     */
    public function addModule($moduleId, $professorIds)
    {
        $this->modules[$moduleId] = new Module($moduleId, $professorIds);
    }

    /**
     * Add a group to this timetable
     *
     * @param int $groupId ID of group
     * @param int $groupSize Size of the group
     * @param array $moduleIds IDs of modules
     */
    public function addGroup($groupId, $moduleIds)
    {
        $this->groups[$groupId] = new Group($groupId, $moduleIds);
        $this->numClasses = 0;
    }

    /**
     * Add a new timeslot
     *
     * @param int $timeslotId ID of time slot
     * @param string $timeslot Timeslot
     */
    public function addTimeslot($timeslotId, $next)
    {
        $this->timeslots[$timeslotId] = new Timeslot($timeslotId, $next);
    }

    /**
     * Create classes using individual's chromosomes
     *
     * @param Individual $individual Individual
     */
    public function createClasses($individual)
    {
        $classes = [];

        $chromosome = $individual->getChromosome();
        $chromosomePos = 0;
        $classIndex = 0;

        foreach ($this->groups as $id => $group) {
            $moduleIds = $group->getModuleIds();

            foreach ($moduleIds as $moduleId) {
                $module = $this->getModule($moduleId);

                for ($i = 1; $i <= $module->getSlots($id); $i++) {
                    $classes[$classIndex] = new CollegeClass($classIndex, $group->getId(), $moduleId);

                    // Add timeslot
                    $classes[$classIndex]->addTimeslot($chromosome[$chromosomePos]);
                    $chromosomePos++;

                    // Add room
                    $classes[$classIndex]->addRoom($chromosome[$chromosomePos]);
                    $chromosomePos++;

                    // Add professor
                    $classes[$classIndex]->addProfessor($chromosome[$chromosomePos]);
                    $chromosomePos++;

                    $classIndex++;
                }
            }
        }

        $this->classes = $classes;
    }

    /**
     * Get the string that shows how the timetable chromosome is to be read
     *
     * @return string Chromosome scheme
     */
    public function getScheme()
    {
        $scheme = [];

        foreach ($this->groups as $id => $group) {
            $moduleIds = $group->getModuleIds();

            $scheme[] = 'G' . $id;

            foreach ($moduleIds as $moduleId) {
                $module = $this->getModule($moduleId);

                for ($i = 1; $i <= $module->getSlots($id); $i++) {
                    $scheme[] = $moduleId;
                }
            }
        }

        return implode(",", $scheme);
    }

    /**
     * Get a room by ID
     *
     * @param int $roomId ID of room
     */
    public function getRoom($roomId)
    {
        if (!isset($this->rooms[$roomId])) {
            print "No room with ID " . $roomId;
            return null;
        }

        return $this->rooms[$roomId];
    }

    /**
     * Get all rooms
     *
     * @return array Collection of rooms
     */
    public function getRooms()
    {
        return $this->rooms;
    }

    /**
     * Get a random room
     *
     * @return Room room
     */
    public function getRandomRoom()
    {
        return $this->rooms[array_rand($this->rooms)];
    }

    /**
     * Get professor with given ID
     *
     * @param int $professorId ID of professor
     */
    public function getProfessor($professorId)
    {
        return $this->professors[$professorId];
    }

    /**
     * Get module by Id
     *
     * @param int $moduleId ID of module
     */
    public function getModule($moduleId)
    {
        return $this->modules[$moduleId];
    }

    /**
     * Get modules of a student group with given ID
     *
     * @param int $groupId ID of group
     */
    public function getGroupModules($groupId)
    {
        $group = $this->groups[$groupId];
        return $group->getModuleIds();
    }

    /**
     * Get a group using its group ID
     *
     * @param int $groupId ID of group
     * @return Group A group
     */
    public function getGroup($groupId)
    {
        return $this->groups[$groupId];
    }

    /**
     * Get timeslot with given ID
     *
     * @param int $timeslotId ID Of timeslot
     * @return Timeslot A timeslot
     */
    public function getTimeslot($timeslotId)
    {
        return $this->timeslots[$timeslotId];
    }

    /**
     * Get a random time slot
     *
     * @return Timeslot A timeslot
     */
    public function getRandomTimeslot()
    {
        return $this->timeslots[array_rand($this->timeslots)];
    }

    /**
     * Get a collection of classes
     *
     * @return array Classes
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Get number of classes that need scheduling
     *
     * @return int Number of classes
     */
    public function getNumClasses()
    {
        if ($this->numClasses > 0) {
            return $this->numClasses;
        }

        $numClasses = 0;

        foreach ($this->groups as $group) {
            $numClasses += count($group->getModuleIds());
        }

        $this->numClasses = $numClasses;
        return $numClasses;
    }

    /**
     * Get classes scheduled for a given day for a given group
     *
     * @param $dayId ID of day we are getting classes for
     * @param $groupId The ID of the group
     */
    public function getClassesByDay($dayId, $groupId)
    {
        $classes = [];

        foreach ($this->classes as $class) {
            $timeslot = $this->getTimeslot($class->getTimeslotId());

            $classDayId = $timeslot->getDayId();

            if ($dayId == $classDayId && $class->getGroupId() == $groupId) {
                $classes[] = $class;
            }
        }

        return $classes;
    }


    private function timeslotsOverlap($timeslotA, $timeslotB)
{
    // Assuming timeslot has getStartTime() and getEndTime() methods
    return !($timeslotA->getEndTime() <= $timeslotB->getStartTime() || $timeslotA->getStartTime() >= $timeslotB->getEndTime());
}

private function isDuringBreak($time)
{
    // Extract start and end times from the time string
    list($startTime, $endTime) = explode(' - ', $time);
    
    // Check if the class time overlaps with break time (12:00 - 14:00)
    return ($startTime < '14:00' && $endTime > '12:00');
}

    /**
     * Calculate the number of clashes
     *
     * @return string Number of clashes
     */
    public function calcClashes()
    {
        $clashes = 0;
        $days = Day::all();
         $classClashes = [];
         // Create an array to hold classes with their ranks
         //$classesWithRanks = [];

             // Define break time range
             $breakStart = '12:00';
             $breakEnd = '13:00';

        foreach ($this->classes as $id => $classA) {
            $roomCapacity = $this->getRoom($classA->getRoomId())->getCapacity();
            $groupSize = $this->getGroup($classA->getGroupId())->getSize();
            $professor = $this->getProfessor($classA->getProfessorId());
            $timeslot = $this->getTimeslot($classA->getTimeslotId());
            $module = $this->getModule($classA->getModuleId());
            $room = $this->getRoom($classA->getRoomId());
           // $module = $this->getModule($classA->getModuleId());

             // Initialize clash count for the current class
             $classClashes[$classA->getId()] = 0;

               // Check if the class is scheduled during break time
        if ($this->isDuringBreak($timeslot->getTime())) {
            echo 'duirng break time --- |>'.$timeslot->getTime()."\n";
            $clashes++;
            $classClashes[$classA->getId()]++;
        }

            // Check for overlapping timeslots within the same day
        foreach ($this->classes as $id => $classB) {
            if ($classA->getId() != $classB->getId()) {
                $timeslotA = $this->getTimeslot($classA->getTimeslotId());
                $timeslotB = $this->getTimeslot($classB->getTimeslotId());

                // Check if they are on the same day and if the timeslots overlap
                if ($timeslotA->getDayId() == $timeslotB->getDayId() && $this->timeslotsOverlap($timeslotA, $timeslotB))
                {
                 // $clashes +=100;
                 $clashes++;
                  $classClashes[$classA->getId()]++;
                 // echo("Overlap detected between Class ID {$classA->getId()} and Class ID {$classB->getId()}");
                }
            }
        }


             // Check if room type matches course requirements
             if ($room->getRoomType() !== $module->getRequiredRoomType()) {
             //$clashes += 10; // Higher penalty for wrong room type
             $clashes++;
              $classClashes[$classA->getId()]++;
             }

            if ($roomCapacity < $groupSize) {
                //$clashes+=100;
                $clashes++;
                 $classClashes[$classA->getId()]++;
            }

            // Check if credits match
            if ($module->getCreditasString() != $timeslot->getCredit()) {
         //  $clashes +=100; // Increment clashes if credits do not match
              $clashes++;
               $classClashes[$classA->getId()]++;
            }

            

            // Check if we don't have any lecturer forced to teach at his occupied time
            if (in_array($timeslot->getId(), $professor->getOccupiedSlots())) {
                $clashes++;
                 $classClashes[$classA->getId()]++;
            }

            // Check if room is taken
            foreach ($this->classes as $id => $classB) {
                if ($classA->getId() != $classB->getId()) {
                    if (($classA->getRoomId() == $classB->getRoomId()) && ($classA->getTimeslotId() == $classB->getTimeslotId())) {
                        $clashes++;
                         $classClashes[$classA->getId()]++;
                        break;
                    }
                }
            }


            if (in_array($classA->getRoomId(), $this->getGroup($classA->getGroupId())->getUnavailableRooms())) {
                $clashes++;
                 $classClashes[$classA->getId()]++;
            }

            // Check if professor is available
            foreach ($this->classes as $id => $classB) {
                if ($classA->getId() != $classB->getId()) {
                    if (($classA->getProfessorId() == $classB->getProfessorId())
                        && ($classA->getTimeslotId() == $classB->getTimeslotId())
                    ) {
                        $clashes++;
                         $classClashes[$classA->getId()]++;
                        break;
                    }
                }
            }

            // Check if we don't have same group in two classes at same time
            foreach ($this->classes as $id => $classB) {
                if ($classA->getId() != $classB->getId()) {
                    if (($classA->getGroupId() == $classB->getGroupId()) && ($classA->getTimeslotId() == $classB->getTimeslotId())) {
                        $clashes++;
                         $classClashes[$classA->getId()]++;
                        break;
                    }
                }
            }
        }

        // Constraint to ensure that no course occurs at two different locations
        // and or at non-consecutive time slots
        foreach ($days as $day) {
            foreach ($this->getGroups() as $group) {
                $classes = $this->getClassesByDay($day->id, $group->getId());
                $checkedModules = [];

                foreach ($classes as $classA) {
                    if (!in_array($classA->getModuleId(), $checkedModules)) {
                        $moduleTimeslots = [];

                        foreach ($classes as $classB) {
                            if ($classA->getModuleId() == $classB->getModuleId()) {
                                if ($classA->getRoomId() != $classB->getRoomId()) {
                                    $clashes++;
                                     $classClashes[$classA->getId()]++;
                                }

                                $moduleTimeslots[] = $classB->getTimeslotId();
                            }
                        }

                        if (!$this->areConsecutive($moduleTimeslots)) {
                            $clashes++;
                             $classClashes[$classA->getId()]++;
                        }

                        $checkedModules[] = $classA->getModuleId();
                    }
                }
            }
        }


        // // Echo the class clashes
        // foreach ($classClashes as $classId => $clashCount) {
        // echo "Class ID: $classId has $clashCount clashes.\n";
        // }

        return $clashes;
    }

    /**
     * Determine whether a given set of numbers are
     * consecutive
     */
    public function areConsecutive($numbers)
    {
        sort($numbers);

        $min = $numbers[0];
        $max = $numbers[count($numbers) - 1];

        for ($i = $min; $i <= $max; $i++) {
            if (!in_array($i, $numbers)) {
                return false;
            }
        }

        return true;
    }

    
}