<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Helper\GlobalHelper;
use Illuminate\Http\Request;

class VendorController extends Controller
{

    public function nonPaginatedData(){
        $query = Vendor::whereIn('status', ['active', 'Active'])->orderBy('title', 'ASC')->get();

        return response()->json($query, 200);
    }

    public function fetchData(Request $request){
        $paginate = $request->show;
        $search = $request->search;

        $sort = "";
        $orderBy = $request['sort'];

        $dataObj = new Vendor;

        if($orderBy){
            $orderBy = json_decode($orderBy);
            $field = $orderBy[0];
            $sort = $orderBy[1];
            $dataObj = $dataObj->orderBy($field, $sort)->with('profile','category');
        }else{
            $dataObj = $dataObj->orderBy('status', 'ASC')->orderBy('title', 'ASC')->with('profile','category');
        }

        if($search){
            $dataObj->where(function($q) use($search){
                $q->where('title', 'like', '%'.$search.'%')
                ->orWhere('contact_name', 'like', '%'.$search.'%');
            });

            $dataObj = $dataObj->get();
            $dataArray['data'] = $dataObj->toArray();
        }else{
            $dataArray = $dataObj->paginate($paginate);
        }

        return response()->json($dataArray, 200);
    }

    public function storeUpdate(Request $request){
        $saveData = array(
                    'title' => $request->title,
                    'contact_name' => $request->contact_name,
                    'profile_id' => $request->profile_id,
                    'brand' => $request->brand,
                    'origin' => $request->origin,
                    'designation' => $request->designation,
                    'address' => $request->address,
                    'contact_no'  => $request->contact_no,
                    'contact_email'  => $request->contact_email,
                    'category_id'  => @$request->category_id,
                    'remarks'  => @$request->remarks,
                );
        if($request->id){
            $query = Vendor::where('id', $request->id)->first();
            $query->update($saveData);
            $message = 'Data has been updated';
            $log_type = 'update';
        }else{
            $query = Vendor::create($saveData);
            $message = 'Data has been created';
            $log_type = 'new';
        }

        $helper = new GlobalHelper;
        $helper->createLogs($query, $request->profile_id, $log_type, $query);

        return response()->json(array('message' => $message), 200);
    }

    public function statusChangeData(Request $request){

        $query = Vendor::where('id', $request->id)->first();
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

    /**
     * List for pinia state
     */
    public function getVendorList() {
        $data = Vendor::where('status', 'active')->orderBy('title', 'ASC')->get();
        return response()->json($data, 200);
    }
}
