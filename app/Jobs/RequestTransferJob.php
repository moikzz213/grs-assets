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

class RequestTransferJob implements ShouldQueue
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
       
        $query = Profile::where('id','=', $data->profile_id)->first(); 
        
        if($query->email){
            $this->nextReceiver($query, $data);
        } 
    }  
 
    private function nextReceiver($query, $data){
        if($data->type == 'request'){
            $snNo = "SN-5".$this->pad($data->id, 6);
        }else{
            $snNo = "SN-3".$this->pad($data->id, 6);
        } 

        $toEmail = $query->email;
         
        if($query->on_leave && $query->email_reliever){
            $toEmail = array($query->email, $query->email_reliever);
        } 
       
        $message = 'Dear '. $query->display_name. ",<br/><br/>";
        $message .= 'Your approval is requested<br/>';
        $message .= 'SN No. : '. $snNo."<br/>"; 
        $message .= 'Subject: '. $data->subject. "<br/><br/>"; 

        $randomString = Str::random(50);
        $randomString2 = Str::random(50);
        
        $type = strtoupper($data->type);
        $link = env('VITE_APP_URL').'/pv/employee-signatory/'.$data->type.'/approvals?o='.$data->order."&key=".$randomString."&pid=".$data->profile_id."&pv=".$randomString2."&id=".$data->id;
         
        $data = array("types" => $type, "link" => $link, "message" => $message, 'subject' => "Asset System: ".$type. " ASSET(s) - ".$snNo. " - ". $data->subject);
      
        Mail::to($toEmail)->queue( new RequestTransferMail( $data) ); 
    }

    function pad($num, $size){
        return substr(str_repeat(0, $size).$num, - $size);
    }
}