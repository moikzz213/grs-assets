<?php

namespace App\Jobs;

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use App\Mail\RequestTransferMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class CronJobAssetRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels; 
  
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $message = array('THIS IS JUST A TEST JOB! IGNORE!');
        
        // check if has request assets more than 1
        $dataForm = array("data" => null, 'link' => null, "message" => $message, 'subject' => 'TEST JOB: GRS ASSET SYSTEM'); 
        
        Mail::to('jacob@gagroup.net')->queue( new RequestTransferMail( $dataForm ) );
      
    }
 
}
