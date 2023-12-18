<?php
namespace App\Helper;

class GlobalHelper
{
    public function client_auth() {
        $profile = \App\Models\Profile::whereHas('client_keys', function($q){
            $q->where('key', request()->bearerToken());
        })->first();
        return isset($profile) ? $profile : null;
    }

    public function randomLettersOnly($length)
    {
        $pool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    public function randomStringWithNumbers()
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    public function createLogs($request, $id, $log_type, $data)
    {
        $request->logs()->create([
            'profile_id' => $id,
            'log_type' => $log_type,
            'details' => json_encode($data)
        ]);
        return true;
    }

    public function runCurl($url,$data, $loginpassw){

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
			CURLOPT_POST => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $loginpassw,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
               'Content-Type: application/json'
            ),
         ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo "CURL ERROR - " . curl_error($curl);
        }
        curl_close($curl);

        return json_encode($response);
    }
}
