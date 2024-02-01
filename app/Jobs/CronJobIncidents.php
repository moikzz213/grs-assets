<?php

namespace App\Jobs;

use App\Mail\IncidentMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class CronJobIncidents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $publisherData = $this->data; 
        
        $data = $publisherData['data'];
        $data =  json_decode($data);

        $message = "Dear, <br/><br/>";
        $message .= "There is a pending asset incident that needs your attention. <br/>";
        
        $link = env('VITE_APP_URL').'report-incident';

        $dataArray = array("data" => $data, "link" => $link, "message" => $message,  "date" => Carbon::now(), 'subject' => 'REMINDER: GRS ASSET SYSTEM : INCIDENT');
        Mail::bcc($data)->queue( new IncidentMail( $dataArray) ); 
    }
}
