<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class QueueController extends Controller
{
    public function startQueue(Request $request)
    {
          // Set the maximum execution time to unlimited
          //set_time_limit(0);

        // Start the queue worker
        Artisan::call('queue:work', [
            '--timeout' => 0,
            '--daemon' => true,
        ]);

        return response()->json(['message' => 'Queue worker started.']);
    }
}