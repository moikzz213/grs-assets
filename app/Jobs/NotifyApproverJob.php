<?php

namespace App\Jobs;

use App\Models\Profile; 

use Illuminate\Bus\Queueable; 
use App\Mail\RequestTransferMail; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class NotifyApproverJob implements ShouldQueue
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
       
        if($query->email){
            $this->selfReceiver($query, $data); 
        } 
    } 

    private function selfReceiver($query, $data){
        $snNo = "SN-5".$this->pad($data->id, 6); 
        $toEmail = $query->email;
        $message = 'Dear '. $query->display_name. ",<br/><br/>";
        $message .= 'Thank you for your approval on below Request No.<br/>';
        $message .= 'SN No. : '. $snNo."<br/><br/>";
        $message .= 'If you did not initiate this approval, kindly notify Commercial Manager - Projects.';
        
        $type = strtoupper($data->type); 
         
        $data = array("types" => $type, "link" => '', "message" => $message, 'subject' => "ASSET SYSTEM: ".$type. " ASSET(s)");
    
        Mail::to($toEmail)->queue( new RequestTransferMail( $data) ); 
    } 

    function pad($num, $size){
        return substr(str_repeat(0, $size).$num, - $size);
    }
}