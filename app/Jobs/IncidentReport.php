<?php

namespace App\Jobs;

use App\Models\Location;
use App\Models\Notification;
use App\Mail\IncidentMail; 
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class IncidentReport implements ShouldQueue
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
       
        $query = Notification::where('meta_type','=', 'incident-receiver')->whereNotNull('meta_value')->orderBy('id', 'ASC')->get(); 
        $location = Location::where('id','=', $data->location_id)->first(); 
        $emails = $query->pluck('meta_value');

        if($emails && count($emails) > 0){
            $toEmail = $emails; 
            $message = 'Urgency: '.$this->statusFn($data->urgency)."<br/>";
            $message .= 'Asset Name: '.$data->title."<br/>";
            $message .= 'Location: '.$location->title."<br/><br/>";
            $message .= 'Description: '.'<pre style="font-size:12px;">'.$data->description."</pre>";
            $incident = array("data" => $data, "message" => $message,  "date" => Carbon::now(), 'subject' => "Asset System: New Incident Reported");
            Mail::to($toEmail)->queue( new IncidentMail( $incident) ); 
        }
    }

    private function statusFn($v){
        if($v == 1){
            return 'Normal';
        }elseif($v == 2){
            return 'Medium';
        }else{
            return 'High';
        }
    }
}