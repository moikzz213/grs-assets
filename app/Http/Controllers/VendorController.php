<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
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
            $dataObj = $dataObj->orderBy($field, $sort);
        }else{
            $dataObj = $dataObj->orderBy('status', 'ASC')->orderBy('title', 'ASC');
        }
    
        if($search){
            $dataObj->where(function($q) use($search){
                $q->where('title', 'like', '%'.$search.'%')
                ->orWhere('contact_name', 'like', '%'.$search.'%')
                ->orWhere('contact_no', 'like', '%'.$search.'%');
            }); 

            $dataObj = $dataObj->get();
            $dataArray['data'] = $dataObj->toArray();
        }else{
            $dataArray = $dataObj->paginate($paginate);
        }
       
        return response()->json($dataArray, 200);
    }
}
