<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function fetchData(){
        $query = Notification::orderBy('meta_type', 'ASC')->get(); 
        return response()->json($query, 200);
    }

    public function storeUpdate(Request $request){
        Notification::truncate();
        $query = Notification::insert($request['data']);

        return response()->json(array('message' => 'Data has been saved successfully!'), 200);
    }
}
