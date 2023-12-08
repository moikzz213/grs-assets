<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Jobs\ResetPasswordMail;
use Illuminate\Support\Facades\Hash;

class UserApiController extends Controller
{
    public function login(Request $request)
    {
        // Validate login
        $fields = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Check email
        $user = User::where([
            'username' => $fields['username'],
            'status' => 'active'
        ])->first();

        // Check password
        if(!$user || !Hash::check($fields['password'], $user->password)){
            return response([
                'message' => 'Incorrect login credentials'
            ], 401);
        }

        // Generate Token
        $token = $user->createToken('meluserstoken')->plainTextToken;

        // Response
        return response( [
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function sanctumCheckUser(Request $request)
    {
        $hasToken = auth('sanctum')->check();
        return response()->json([
            "user" => $hasToken ? auth('sanctum')->user() : null,
        ], 200);
    }


    /**
     * Below are unsued
     */
    public function logout(Request $request)
    {
        $fields = $request->validate([
            'username' => 'required|string'
        ]);
        $user = User::where([
            'username' => $fields['username'],
            'status' => 'active'
        ])->first();
        $token = $user->tokens()->delete();
        return response()->json([
            "message" => "Logged out successfully",
        ], 200);
    }

    public function getUser(Request $request)
    {
        $fields = $request->validate([
            'username' => 'required|string'
        ]);
        $user = User::where([
            'username' => $fields['username'],
            'status' => 'active'
        ])->firstOrFail();

        // $hasToken = auth('sanctum')->check();

        if($user->tokens() != ""){
            $hasToken = true;
        }

        return response()->json([
            "user" => $user,
            "token" => $hasToken
        ], 200);
    }

    public function resetPasswordMail(Request $request){
       
        $query = Profile::where(['ecode' => $request->ecode])->whereIn('status', ['active','Active'])->first();
        $sendTo = '';
        $baseURL = env("VITE_APP_URL");
       
        if($query){
            if($query->email){
                $sendTo = $query->email;
                $subMsg = 'email ('.$query->email.')';
                $mailMsg = 'You are receiving this email because we received a password reset request for your account.';
                $mailMsg2 = 'If you did not request a password reset, no further action is required.';

                $link = $baseURL.'/link/reset-password/employee-ecode?key=Gtj1a5A$34zAs%$ajx98AzkIhg(65sv=1Lk8BcWAawg73&ecode='.$query->ecode."&ec=mCA%qIBQOdLR3mQzAkybITmcF4UOIYL%LosC6a$*Qlw5$77WDSLbfrdvGaXNy2)pv";
                $msg = 'Email has been sent to your '.$subMsg;
    
                ResetPasswordMail::dispatchAfterResponse(['email' => $sendTo,'link' => $link, 'message' => $mailMsg, 'message2' => $mailMsg2])->onQueue('processing');
            }  

        }elseif(!$request->ecode){
            $msg = "Data is Invalid!";
        }else{
            $msg = "Employee code is invalid / Your account is disabled. Contact your HRBP.";
        }  
        return response([
            'message' => $msg
        ], 200);         
    }
    
}
