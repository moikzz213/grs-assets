<?php

namespace App\Jobs;

use App\Mail\ResetMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ResetPasswordMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

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
        $link = $publisherData['link'];
        $toEmail = $publisherData['email'];
        $message = $publisherData['message'];
        $message2 = $publisherData['message2'];
        $data = array("link" => $link,"message" => $message, 'message2' => $message2,  "date" => Carbon::now(), 'subject' => "GRS ASSET SYSTEM: Reset Password");
        Mail::to($toEmail)->queue( new ResetMail( $data) ); 
    }
}
