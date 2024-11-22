<?php
namespace App\Services\GeneticAlgorithm;

use App\Models\Course;
use App\Models\CollegeClass as CollegeClassModel;

class Module
{
    /**
     * Id of module
     *
     * @var int
     */
    private $moduleId;

    /**
     * Course type
     * @var string
     */
    private $courseType;

    /**
     * Module's code
     *
     * @var string
     */
    private $moduleModel;

    /**
     * IDs of professors handling this course
     *
     * @var array
     */
    private $professorIds;

    /**
     * Number of allocations done for this module so far
     *
     * @var int
     */
    private $allocatedSlots;

    /**
     * Create a new module
     *
     * @param int $moduleId ID of module or course
     * @param array  $professorIds Professors treating this module
     */
    public function __construct($moduleId, $professorIds)
    {
        $this->moduleId = $moduleId;
        $this->moduleModel = Course::find($moduleId);
        $this->professorIds = $professorIds;
        $this->allocatedSlots = 0;
        $this->courseType = $this->moduleModel->course_type;
    }

    /**
     * Get ID of a module
     *
     * @return int ID Of module
     */
    public function getModuleId()
    {
        return $this->moduleId;
    }

     public function getCredit()
     {
     return $this->moduleModel->credit;
     }

     public function getCreditasString()

     {
        $credit = $this->moduleModel->credit;
        $str = strval($credit);
        return $str;
     }

    /**
     * Get the code of the module
     *
     * @return string Code of the module
     */
    public function getModuleCode()
    {
        return $this->moduleModel->course_code;
    }

    /**
     * Get the module name
     *
     * @return string Module name
     */
    public function getName()
    {
        return $this->moduleModel->name;
    }

    /**
     * Get the number of slots this module should take
     *
     * @return int The number of slots
     */
    public function getSlots($groupId)
    {
        $group = CollegeClassModel::find($groupId);
        return $group->courses()->where('courses.id', $this->moduleId)->first()->pivot->meetings ;
    }

    /**
     * Get the slots of this module allocated so far
     *
     * @return int Allocated slots
     */
    public function getAllocatedSlots()
    {
        return $this->allocatedSlots;
    }

    public function resetAllocated()
    {
        $this->allocatedSlots = 0;
    }

    /**
     * Increase the count of slots allocated so far
     *
     * @return void
     */
    public function increaseAllocatedSlots()
    {
        $this->allocatedSlots += 1;
    }

    /**
     * Get a random professor that can teach this module
     *
     * @return int ID of professor
     */
    public function getRandomProfessorId()
    {
        $pos = rand(0, count($this->professorIds) - 1);
        return $this->professorIds[$pos];
    }

     public function getRequiredRoomType() {
     return $this->courseType; // Return the required room type
     }
}