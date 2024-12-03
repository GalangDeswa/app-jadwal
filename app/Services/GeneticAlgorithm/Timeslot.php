<?php
namespace App\Services\GeneticAlgorithm;

use App\Models\Timeslot as TimeslotModel;
use App\Models\Day as DayModel;

class Timeslot
{
    /**
     * ID Of time slot
     *
     * @var int
     */
    private $timeslotId;

    /**
     * Model of day from database
     *
     * @var App\Models\Day;
     */
    private $dayModel;

    /**
     * Model of timeslot from database
     *
     * @var App\Models\Timeslot
     */
    private $timeslotModel;

    /**
     * ID of next timeslot
     */
    private $nextSlot;

    private $credit;
    private $rank;
    private $time;
  private $startTime; // e.g., '08:00'
  private $endTime; // e.g., '09:00'

    /**
     * Create a timeslot
     *
     * @param $timeslotId Id of timeslot
     * @param $nextSlot Id of next time slot
     */
    public function __construct($timeslotId, $nextSlot)
    {
        $this->timeslotId = $timeslotId;
        $this->nextSlot = $nextSlot;

        $matches = [];
        preg_match('/D(\d*)T(\d*)/', $timeslotId, $matches);

        $dayId = $matches[1];
        $timeslotId = $matches[2];

        $this->dayModel = DayModel::find($dayId);
        $this->timeslotModel = TimeslotModel::find($timeslotId);
        $this->credit = $this->timeslotModel->credit;
        $this->rank = $this->timeslotModel->rank;
        $this->time = $this->timeslotModel->time;
        
        $timeParts = explode(' - ', $this->timeslotModel->time);
        $this->startTime = $timeParts[0]; // Get the start time
        $this->endTime = $timeParts[1];



//         echo "dari timeslot php---------------------------------------->"."\n";
//         echo "Day ID: $dayId, Timeslot ID: $timeslotId\n"."\n";
//         foreach($matches as $x){
//             echo "----------matches gabungan------------"."\n";
//             echo $x;
//  }
        }
      

    /**
     * Get ID of timeslot
     *
     * @return int Id of timeslot
     */
    public function getId()
    {
        return $this->timeslotId;
    }

     public function getCredit()
     {
      return $this->credit;
     }

     public function getRank()
     {
     return $this->rank;
     }

    /**
     * Get timeslot
     *
     * @return int Timeslot
     */
    public function getTimeslot()
    {
        return $this->dayModel->short_name . ' ' . $this->timeslotModel->time;
    }

    /**
     * Get the id of time slot after this
     *
     * @return int ID of next timeslot
     */
    public function getNext()
    {
        return $this->nextSlot;
    }
 
    public function getDayId()
    {
        return $this->dayModel->id;
    }

    public function getTimeslotId()
    {
        return $this->timeslotModel->id;
    }


    public function getStartTime()
    {
    return $this->startTime;
    }

    public function getEndTime()
    {
    return $this->endTime;
    }

    public function getTime(){
        return $this->time;
    }

      // Method to get the time range in the desired format
      public function getTimeRange()
      {
      return $this->startTime . ' - ' . $this->endTime;
      }
}