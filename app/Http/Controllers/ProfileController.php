<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\ClientKey;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function saveProfile(Request $request)
    {
        $profileArray = array(
            'full_name' => $request['full_name'],
            'dob' => $request['dob'],
            'nationality' => $request['nationality'],
        );
        $profile = Profile::updateOrCreate([
            'user_id' => $request['id'],

        ], $profileArray);
        return response()->json([
            'message' => 'Profile saved successfully',
            'profile' => $profile
        ], 200);
    }

    public function getProfileById($id)
    {
        $profile = Profile::where('user_id', $id)->first();
        return response()->json($profile, 200);
    }

    public function fetchProfile($ecode, $token)
    { 
        $data = json_decode($ecode);
        
        $profile = Profile::where('ecode', $data->username)->with('access')->first(); 

        $clientKey = ClientKey::firstOrCreate([
            'key' => $token,
            'username' => $data->username,
        ]); 

        return response()->json($profile, 200);
    }
}
