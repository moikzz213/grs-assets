<?php

namespace App\Jobs;

use App\Models\Profile;
 
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use App\Mail\RequestTransferMail; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class RejectMailJob implements ShouldQueue
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

        $receiverType = $data->typeReceiver;

        $query = Profile::where('id','=', $data->profile_id)->first(); 
        
        if($receiverType == 'requestor' && $query->email){
            $this->sendToRequestor($query, $data);
        } 
    }  
 
    private function sendToRequestor($query, $data){
        $snNo = "SN-5".$this->pad($data->id, 6);
        $toEmail = $query->email;
        $message = 'Dear '. $query->display_name. ",<br/><br/>";
        $message .= 'Your request has been rejected.<br/>';
        $message .= 'SN No. : '. $snNo."<br/><br/>";
        $message .= 'Kindly check the link below for details.<br/>'; 

        $randomString = Str::random(50);
        $randomString2 = Str::random(50);
        
        $type = strtoupper($data->type);
        $link = env('VITE_APP_URL').'request-asset/update/id/'.$data->id;
         
        $data = array("types" => $type, "link" => $link, "message" => $message, 'subject' => "Asset System: ".$type. " ASSET(s)");
    
        Mail::to($toEmail)->queue( new RequestTransferMail( $data) ); 
    }

    function pad($num, $size){
        return substr(str_repeat(0, $size).$num, - $size);
    }
}