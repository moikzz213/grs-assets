<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use App\Helper\GlobalHelper;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    public function nonPaginatedData(){
        $query = Warranty::orderBy('warranty_title', 'ASC')->get();
        return response()->json($query, 200);
    }

    public function fetchData(Request $request){
        $paginate = $request->show;
        $search = $request->search;

        $sort = "";
        $orderBy = $request['sort'];

        $dataObj = new Warranty;

        if($orderBy){
            $orderBy = json_decode($orderBy);
            $field = $orderBy[0];
            $sort = $orderBy[1];
            $dataObj = $dataObj->orderBy($field, $sort)->with('vendor');
        }else{
            $dataObj = $dataObj->orderBy('id', 'DESC')->with('vendor');
        }

        if($search){
            $dataObj->where(function($q) use($search){
                $q->where('warranty_title', 'like', '%'.$search.'%');
            });

            $dataObj = $dataObj->get();
            $dataArray['data'] = $dataObj->toArray();
        }else{
            $dataArray = $dataObj->paginate($paginate);
        }

        return response()->json($dataArray, 200);
    }

    public function fetchWarrantyById($id){
        $query = Warranty::where('id', $id)->with('pivot_assets','attachment')->first();

        return response()->json(array('data' => $query), 200);
    }

    public function syncImages(Request $request) {
        $query = Warranty::where('id', $request->id)->with('pivot_assets','attachment')->first();
        $query->attachment()->sync($request['file_ids']);
        $log_type = 'delete';
        if(count($request['file_ids']) > 0){
            $log_type = 'add-update'; 
        }

        $helper = new GlobalHelper;
        $helper->createLogs($query, $request->profile_id, $log_type, $query);
        return response()->json($query, 200);
    }

    public function storeUpdate(Request $request){
        $data = array('warranty_title' => $request->warranty_title, 'warranty_start_date' => $request->warranty_start_date,
        'warranty_end_date' => $request->warranty_end_date,'vendor_id' => $request->vendor_id, 'amc_start_date'  => $request->vendor_id,  'amc_end_date'  => $request->amc_end_date);
        if($request->id){
            $query = Warranty::where('id', $request->id)->first();
            $query->update($data);
            $message = 'Data has been updated';
            $log_type = 'update';

            $query->pivot_assets()->sync($request['assets']); 
             
        }else{
            $query = Warranty::create($data);
            $message = 'Data has been created';
            $log_type = 'new';
        }

        $helper = new GlobalHelper;
        $helper->createLogs($query, $request->profile_id, $log_type, $query);

        return response()->json(array('message' => $message), 200);
    }

    public function statusChangeData(Request $request){

        $query = Warranty::where('id', $request->id)->first();
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