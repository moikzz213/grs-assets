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
        $data = json_decode($data);
        $email = $data->email;
        $randomString = Str::random(50);
        $randomString2 = Str::random(50);

        if($data->on_leave && $data->email_reliever){
            $email = array($data->email, $data->email_reliever);
        } 

        $baseURL = env('VITE_APP_URL').'/pv/employee-signatory/';
        $message = 'Dear, <br/>Waiting for you to sign. Click the link/s to review and sign<br/>';
        $message .= '<table width="600">';
        foreach ($data->reminder_profile as $key => $v) {

            // get the approval_requests with awaiting-approval status
            $awaitingApproval = collect($v->request_approvals)
            ->where('request_asset_id', $v->id)
            ->where('status', 'awaiting-approval')
            ->first();

            $title = 'SN-5';
            $header = 'Request Asset';
            if($v->types == 'transfer'){
                $title = 'SN-3';
                $header = 'Transfer Asset';
            }

            // set the list of requests in email body
            if($awaitingApproval){
                $message .= "<tr><td>{$header}: <a href='{$baseURL}{$v->types}/approvals?o={$awaitingApproval->orders}&key={$randomString}&pid={$data->id}&pv={$randomString2}&id={$v->id}'>{$title}{$this->pad($v->id,6)}</a> ( {$v->subject} )</td></tr>";
            }
        }
        $message .= '</table>';

        // check if has request assets more than 1
        $dataForm = array("data" => $data, 'link' => null, "message" => $message, 'subject' => 'REMINDER: GRS ASSET SYSTEM : APPROVAL');

        // only send the email if profile has request assets
        if(count($data->reminder_profile) > 0){
            Mail::to($email)->queue( new RequestTransferMail( $dataForm ) );
        }
    }

    function pad($num, $size){
        return substr(str_repeat(0, $size).$num, - $size);
    }
}
