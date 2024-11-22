<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\GeneticAlgorithm\TimetableRenderer;
use Illuminate\Support\Facades\Log;

class RenderTimetables implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $timetable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($timetable)
    {
        $this->timetable = $timetable;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            Log::info('Generating timetable render');
             $renderer = new TimetableRenderer($this->timetable);
             $renderer->render();

        }catch(\Throwable $th){
              Log::error('Error in Generating timetable render: ' . $th->getMessage(), [
              'file' => $th->getFile(),
              'line' => $th->getLine(),
              'trace' => $th->getTraceAsString() ]);

              }
        }
       
    
}