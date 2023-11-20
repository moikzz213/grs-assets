<?php

namespace App\Jobs;

use App\Models\Profile;
use App\Helper\GlobalHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class accessUms implements ShouldQueue
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
        $helper = new GlobalHelper();
        
        $ecode = $publisherData['ecode'];
        $status = $publisherData['status'];
        $app = env('VITE_APP_KEY');
        $username = env('API_APP_USER');
        $password = env('API_APP_PASS');
        $serverURL = env('VITE_SANCTUM_USER_URL');

        $data = ['app' => $app, 'ecode' => $ecode, 'status' => $status];
        $loginpassw = $username . ':' . $password;
        $url = $serverURL.'/api/other-application/access/add-update';

        $response = $helper->runCurl($url, $data, $loginpassw); 
    }
}
