<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Helper\GlobalHelper;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    
    public function fetchData(){
        $query = Company::whereIn('status', ['active', 'Active'])->orderBy('title', 'ASC')->get();

        return response()->json($query, 200);
    }

    public function fetchDataObj(Request $request){
        $paginate = $request->show;
        $search = $request->search;

        $sort = "";
        $orderBy = $request['sort'];

        $dataObj = new Company;
        
        if($orderBy){
            $orderBy = json_decode($orderBy);
            $field = $orderBy[0];
            $sort = $orderBy[1];
            $dataObj = $dataObj->orderBy($field, $sort)->with('profile');
        }else{
            $dataObj = $dataObj->orderBy('status', 'ASC')->orderBy('title', 'ASC')->with('profile');
        }
    
        if($search){
            $dataObj->where(function($q) use($search){
                $q->where('title', 'like', '%'.$search.'%')
                ->orWhere('code', 'like', '%'.$search.'%');
            });

            $dataObj = $dataObj->get();
            $dataArray['data'] = $dataObj->toArray();
        }else{
            $dataArray = $dataObj->paginate($paginate);
        }
       
        return response()->json($dataArray, 200);
    }

    public function storeUpdate(Request $request){
     
        if($request->id){
            $query = Company::where('id', $request->id)->first();
            $query->update(array('title' => $request->title, 'code' => $request->code,'profile_id' => $request->profile_id));
            $message = 'Data has been updated';
            $log_type = 'update';
        }else{
            $query = Company::create(array('title' => $request->title, 'code' => $request->code,'profile_id' => $request->profile_id));
            $message = 'Data has been created';
            $log_type = 'new';
        }

        $helper = new GlobalHelper;
        $helper->createLogs($query, $request->profile_id, $log_type, $query);

        return response()->json(array('message' => $message), 200);
    }

    public function statusChangeData(Request $request){

        $query = Company::where('id', $request->id)->first();
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