<?php

namespace App\Jobs;

use App\Models\Status;
use App\Models\Profile;
use App\Models\Location;
use App\Mail\IncidentMail; 
use App\Models\Notification;
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
        if($data->type_id == 2){
            $receiver = 'maintenance-receiver';
            $subject = "Asset System: Asset Maintenance";
            $subLink = 'maintenance/update/id/';
        }else{
            $receiver = 'incident-receiver';
            $subject = "Asset System: Incident Reported";
            $subLink = 'report-incident/update/id/';
        }
        if(@$data->handled_by){
            $query = Profile::where('id','=', $data->handled_by)->first(); 
           
            $emails = array($query->email);
        }else{
            $query = Notification::where('meta_type','=', $receiver)->whereNotNull('meta_value')->orderBy('id', 'ASC')->get(); 
            $emails = $query->pluck('meta_value');
        }

        $location = Location::where('id','=', $data->location_id)->first();
        
        if($emails && count($emails) > 0){
            $toEmail = $emails;  

            $message = '<table width="600"><tr><td width="150">Urgency:</td><td>'.$this->statusFn($data->urgency_id).'</td></tr>';
            $message .= '<tr><td>Asset Name:</td><td>'.$data->title.'</td></tr>';
            $message .= '<tr><td>Location:</td><td>'.$location->title.'</td></tr>';
            $message .= '<tr><td>Description</td><td>'.$data->description.'</td></tr></table>';

            $link = env('VITE_APP_URL').$subLink. $data->id;

            $incident = array("data" => $data, "link" => $link, "message" => $message,  "date" => Carbon::now(), 'subject' => $subject);
            Mail::to($toEmail)->queue( new IncidentMail( $incident) ); 
        }
    }

    private function statusFn($v){
        $query = Status::select('title')->where('id', $v)->first();
        return $query->title;
    }
}