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
            'display_name' => $request['display_name'],
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'], 
            'email' => $request['email'],
            'contact' => $request['contact'],
            'status' => $request['status'],
            'role' => $request['role'], 
        );
        if(@$request['company_id']){
            $company = array('company_id' => $request['company_id']);
            $profileArray = array_merge($profileArray, $company);
        }
      
        $profile = Profile::updateOrCreate([
            'id' => $request['id'], 
        ], $profileArray);

        return response()->json([
            'message' => 'Profile saved successfully',
            'profile' => $profile
        ], 200);
    }

    public function createNewProfile(Request $request)
    { 
        $profileArray = array(
            'display_name' => $request['display_name'],
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'company_id' => $request['company_id'], 
            'email' => $request['email'],
            'contact' => $request['contact'],
            'role' => $request['role'], 
            'ecode' => $request['ecode'], 
            'username' => $request['ecode'], 
            'designation' => $request['designation'], 
        );
        $profile = Profile::create($profileArray);

        return response()->json([
            'message' => 'New Profile has successfully created',
            'profile' => $profile
        ], 200);
    }

    public function storePageCapabilities(Request $request){
       
        $profile = Profile::where('id', $request->profileID)->first(); 
        $profile->access()->delete();

        foreach ($request->data as $key => $value) { 
            $profile->access()->create(['slug' => $value['slug'], 'capabilities' => @$value['capabilities'] ? json_encode($value['capabilities']) : '']);
        }
        
        return response()->json([
            'message' => 'Access has been updated'            
        ], 200);
    }

    public function getProfileById($id){
        $profile = Profile::where('id', $id)->with('access')->first();
        return response()->json($profile, 200);
    }

    public function fetchProfile($ecode, $token){ 
        $data = json_decode($ecode);
        
        $profile = Profile::where('ecode', $data->username)->with('access')->first(); 

        $clientKey = ClientKey::firstOrCreate([
            'key' => $token,
            'username' => $data->username,
        ]); 

        return response()->json($profile, 200);
    }
}
