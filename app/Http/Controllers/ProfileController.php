<?php

namespace App\Http\Controllers;

use App\Jobs\accessUms;
use App\Models\Profile;
use App\Models\ClientKey;
use App\Helper\GlobalHelper;
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
            'designation' => $request['designation'], 
        );
        if(@$request['company_id']){
            $company = array('company_id' => $request['company_id']);
            $profileArray = array_merge($profileArray, $company);
        }
       
        $profile = Profile::updateOrCreate([
            'id' => $request['id'], 
        ], $profileArray);

        $helper = new GlobalHelper;
        $helper->createLogs($profile, $request['profile_id'], 'update', $profile);
        

        return response()->json([
            'message' => 'Profile saved successfully',
            'profile' => $profile
        ], 200);
    }

    public function createNewProfile(Request $request)
    { 
        $request->validate([
            'ecode' => 'required|string'          
        ]);
       
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

        accessUms::dispatch(['ecode' => $request['ecode'], 'status' => 'active'])->onQueue('default'); 

        $helper = new GlobalHelper;
        $helper->createLogs($profile, $request['profile_id'], 'new', $profile);
        
        return response()->json([
            'message' => 'New Profile has successfully created',
            'profile' => $profile
        ], 200);
    }

    public function storePageCapabilities(Request $request){
       
        $profile = Profile::where('id', $request->profileID)->first(); 
        $profile->access()->delete();

        $helper = new GlobalHelper;
       
        foreach ($request->data as $key => $value) { 
            $data = $profile->access()->create(['slug' => $value['slug'], 'capabilities' => @$value['capabilities'] ? json_encode($value['capabilities']) : '']);
            $helper->createLogs($data, $request->profile_id, 'update-access', $data);
        }
        
        return response()->json([
            'message' => 'Access has been updated'            
        ], 200);
    }

    public function getProfileById($id){
        $profile = Profile::where('id', $id)->with('access')->first();
        return response()->json($profile, 200);
    }

    public function fetchSignatories(){ 
        $query = Profile::whereNot('role','superadmin')->whereIn('status', ['active', 'Active'])->orderBy('display_name', 'ASC')->get(); 
        return response()->json($query, 200);
    }

    public function fetchProfile($ecode, $token){  
        
        $profile = Profile::whereIn('status', ['active', 'Active'])->where('ecode', $ecode)->with('access')->first(); 
       
        if(!$profile){ 
            return response()->json('Your account has not been created for this application. Contact Administrator', 200);
        }
        $clientKey = ClientKey::firstOrCreate([
            'key' => $token,
            'username' => $ecode,
        ]); 

        return response()->json($profile, 200);
    }

    public function fetchFacilityTeam(){ 
        $query = Profile::where('role','facility')->whereIn('status', ['active', 'Active'])->orderBy('display_name', 'ASC')->get(); 
        return response()->json($query, 200);
    }
}
