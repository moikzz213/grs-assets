<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function fetchData(){
        $query = Notification::orderBy('id', 'ASC')->get(); 
        return response()->json($query, 200);
    }

    public function storeUpdate(Request $request){
       
        Notification::truncate();
        $query = Notification::insert($request['data']);
        
        Log::create(array('details' => json_encode($request['data']), 'profile_id' => $request->profile_id, 'log_type' => 'setup-notification')); 
        
        return response()->json(array('message' => 'Data has been saved successfully!'), 200);
    }

    public function fetchIncidentReceivers(){
        $query = Notification::where('meta_type','=', 'incident-receiver')->whereNotNull('meta_value')->rderBy('id', 'ASC')->get(); 
        return response()->json($query, 200);
    }
}
