<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    

    public function saveProfile(Request $request)
    {
        $profile = Profile::updateOrCreate(
            [
                'user_id' => $request['user_id']
            ],
            [
                'user_id' => $request['user_id'],
                'full_name' => $request['full_name'],
                'dob' => $request['dob'],
                'nationality' => $request['nationality'],
            ]
        );
        return response()->json([
            'message' => 'Profile updated successfully',
        ], 200);
    }

    public function getUsers()
    {  
        $profiles = Profile::orderBy('role', 'ASC')->paginate(10);
        return response()->json($profiles, 200);
    }
    
}
