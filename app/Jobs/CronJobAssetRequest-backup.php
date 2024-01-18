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
        $data =  json_decode($data);
        $email = $data->email;

        $randomString = Str::random(50);
        $randomString2 = Str::random(50);

        $baseURL = env('VITE_APP_URL').'/pv/employee-signatory/';

        $message = 'Dear, <br/>You have a pending approval(s)<br/>Kindly do the needful.<br/>';
        $message .= '<table width="600">';
        foreach ($data->ids as $key => $v) {
            $title = 'SN-5';
            $header = 'Request Asset';
            if($v->type == 'transfer'){
                $title = 'SN-3';
                $header = 'Transfer Asset';
            }

            $message .= "<tr><td>{$header}: <a href='{$baseURL}{$v->type}/approvals?o={$v->orders}&key={$randomString}&pid={$v->profile}&pv={$randomString2}&id={$v->id}'>{$title}{$this->pad($v->id,6)}</a></td></tr>";
        }
        $message .= '</table>';

        $dataForm = array("data" => $data, 'link' => null, "message" => $message, 'subject' => 'REMINDER: GRS ASSET SYSTEM : APPROVAL');
        Mail::to($email)->queue( new RequestTransferMail( $dataForm ) );
    }

    function pad($num, $size){
        return substr(str_repeat(0, $size).$num, - $size);
    }
}
