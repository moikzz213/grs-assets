<?php

namespace App\Jobs;

use App\Models\Profile; 

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable; 
use App\Mail\RequestTransferMail; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ApprovalsCompleted implements ShouldQueue
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
        $request_id = $publisherData['id']; 
        $type = $publisherData['type']; 

        $IDs =  json_decode($data); 
        
        $query = Profile::whereIn('id', $IDs)->pluck('email');  
       
        $this->receivers($query, $IDs[0], $type, $request_id); 
        
    } 

    private function receivers($query,$ID, $type, $request_id){
        $title = 'Request';
        if($type == 'request'){
            $snNo = "SN-5".$this->pad($request_id, 6);  
        }else{
            $title = 'Transfer';
            $snNo = "SN-3".$this->pad($request_id, 6);  
        }
         
        $message = $title.' No. : '. $snNo." has been approved completely<br/><br/>";  

        $randomString = Str::random(50);
        $randomString2 = Str::random(50);
        
        $typeTitle = strtoupper($type); 
        $link = env('VITE_APP_URL').'/pv/employee-signatory/'.$type.'/approvals?o=99&key='.$randomString."&pid=".$ID."&pv=".$randomString2."&id=".$request_id;
        
        $data = array("types" => $typeTitle, "link" => $link, "message" => $message, 'subject' => "Asset System: ".$typeTitle. " ASSET(s) - COMPLETED");
    
        Mail::to($query)->queue( new RequestTransferMail( $data) ); 
    } 

    function pad($num, $size){
        return substr(str_repeat(0, $size).$num, - $size);
    }
}