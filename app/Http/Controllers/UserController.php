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

    public function getUsers(Request $request)
    {
        $paginate = $request->show;
        $search = $request->search;

        $sort = "";
        $orderBy = $request['sort'];

        $profiles = new Profile;
        
        if($orderBy){
            $orderBy = json_decode($orderBy);
            $field = $orderBy[0];
            $sort = $orderBy[1];
            $profiles = $profiles->whereNot('role', 'superadmin')->orderBy($field, $sort);
        }else{
            $profiles = $profiles->whereNot('role', 'superadmin')->orderBy('status', 'ASC')->orderBy('role', 'ASC')->orderBy('first_name', 'ASC');
        }
    
        if($search){
            $profiles->whereNot('role', 'superadmin')->where(function($q) use($search){
                $q->where('ecode', 'like', '%'.$search.'%')
                ->orWhere('username', 'like', '%'.$search.'%')
                ->orWhere('display_name', 'like', '%'.$search.'%')
                ->orWhere('role', 'like', '%'.$search.'%');
            }); 

            $profiles = $profiles->get();
            $dataArray['data'] = $profiles->toArray();
        }else{
            $dataArray = $profiles->paginate($paginate);
        }
       
        return response()->json($dataArray, 200);
    }

    public function validateUser($ecode){
        $query = Profile::where('ecode', $ecode)->first();
        return response()->json($query, 200);
    }
}
