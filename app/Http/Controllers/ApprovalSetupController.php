<?php

namespace App\Http\Controllers;

use App\Helper\GlobalHelper;
use Illuminate\Http\Request;
use App\Models\ApprovalSetup;

class ApprovalSetupController extends Controller
{
    public function nonPaginatedData($type){
        $query = ApprovalSetup::where('type', $type)->whereIn('status', ['active', 'Active'])->orderBy('title', 'ASC')->get();

        return response()->json($query, 200);
    }

    public function fetchDataByID($id){
        $query = ApprovalSetup::where('id', $id)->first(); 
        return response()->json($query, 200);
    }

    public function fetchData(Request $request){
        $paginate = $request->show; 

        $sort = "";
        $orderBy = $request['sort'];
        $type = $request['type'];

        $dataObj = new ApprovalSetup;
        
        if($orderBy){
            $orderBy = json_decode($orderBy);
            $field = $orderBy[0];
            $sort = $orderBy[1];
            $dataObj = $dataObj->where('type', $type)->orderBy($field, $sort)->with('profile');
        }else{
            $dataObj = $dataObj->where('type', $type)->orderBy('status', 'ASC')->orderBy('title', 'ASC')->with('profile');
        }
       
        $dataArray = $dataObj->paginate($paginate); 
       
        return response()->json($dataArray, 200);
    }

    public function storeUpdate(Request $request){
     
        if($request->id){
            $query = ApprovalSetup::where('id', $request->id)->first();
            $query->update(array('title' => $request->title,'profile_id' => $request->profile_id));
            $message = 'Data has been updated';
            $log_type = 'update';
        }else{
            $query = ApprovalSetup::create(array('title' => $request->title,'profile_id' => $request->profile_id));
            $message = 'Data has been created';
            $log_type = 'new';
        }

        $helper = new GlobalHelper;
        $helper->createLogs($query, $request->profile_id, $log_type, $query);

        return response()->json(array('message' => $message), 200);
    }

    public function statusChangeData(Request $request){

        $query = ApprovalSetup::where('id', $request->id)->first();
        if($request->status == 'disabled'){ 
            $status = 'disabled';
            
        }else{
            $status = 'active';
        }
        $log_type = 'change-status';
        
        $message = 'Data has been '.$status;
        $query->update(array('status' => $status,'profile_id' => $request->profile_id));

        $helper = new GlobalHelper;
        $helper->createLogs($query, $request->profile_id, $log_type, $query);

        return response()->json(array('message' => $message), 200);
    }
}
