<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Helper\GlobalHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AssetController extends Controller
{
    public function getAssetById($id) {
        $asset = Asset::where('id', $id)
        ->with(
            'warranty.vendor',
            'maintenance',
            'brand',
            'model',
            'category',
            'company',
            'location',
            'attachments'
        )->first();
        return response()->json($asset, 200);
    }

    public function save(Request $request) {

        $globalHelper = new GlobalHelper;
        $msg = "";
        $statusCode = 200;
        $assetArray = array();
        $asset = null;

        DB::beginTransaction();
        try {
            $assetArray = array(
                'asset_name' => $request['asset_name'],
                'serial_number' => $request['serial_number'],
                'asset_code' => $request['asset_code'],
                'section_code' => $request['section_code'],
                'category_id' => $request['category_id'],
                'company_id' => $request['company_id'],
                'location_id' => $request['location_id'],
                'status_id' => $request['status_id'],
                'specification' => $request['specification'],
                'model_id' => $request['model_id'],
                'brand_id' => $request['brand_id'],
                'condition_id' => $request['condition_id'],
                'author_id' => $globalHelper->client_auth()->id,

                // edit fields
                'last_author_id' => $globalHelper->client_auth()->id,
                'vendor_id' => isset($request['vendor_id']) ? $request['vendor_id'] : null,
                'price' => isset($request['price']) ? $request['price'] : null,
                'po_number' => isset($request['po_number']) ? $request['po_number'] : null,
                'purchased_date' => isset($request['purchased_date']) ? $request['purchased_date'] : null,
                'remarks' => isset($request['remarks']) ? $request['remarks'] : null,
            );

            if(isset($request['id']) && $request['id'] != null){
                $asset = Asset::find($request['id']);
                $asset->attachments()->sync($request['file_ids']);
            }else{
                $asset = Asset::create($assetArray);
                if($asset && $request['file_ids']){
                    $asset->attachments()->sync($request['file_ids']);
                }
            }

            DB::commit();
            $msg = "Asset has been saved ";
        } catch (Exception $e) {
            DB::rollback();
            $statusCode = 500;
            $msg = "Error while saving Asset";
        }

        return response()->json([
            'asset' => $asset,
            'message' => $msg,
        ], $statusCode);
    }

    public function fetchAssetCode($code){
        $query = Asset::where('asset_code', '=',$code)->with('warranty.vendor', 'maintenance','brand','model','category','company','location')->first();
        return response()->json($query, 200);
    }

    public function fetchData(Request $request){
        $paginate = $request->show;
        $search = $request->search; 

        $sort = "";
        $orderBy = $request->sort;
        $filter = $request->filter;
        $filterSearch = json_decode($filter);
       
        $dataObj = new Asset; 

        if($orderBy){
            $orderBy = json_decode($orderBy);   
            $field = $orderBy[0];
            $sort = $orderBy[1];
            $dataObj = $dataObj->orderBy($field, $sort)->with( 'created_by', 'company', 'location', 'brand', 'model','category', 'status', 'condition');
        }else{
            if(@$filterSearch->company_id){
                $dataObj = $dataObj->where('company_id', $filterSearch->company_id);
            }
            if(@$filterSearch->location_id){
                $dataObj = $dataObj->where('location_id', $filterSearch->location_id);
            }
            if(@$filterSearch->brand_id){
                $dataObj = $dataObj->where('brand_id', $filterSearch->brand_id);
            }
            if(@$filterSearch->status_id){
                $dataObj = $dataObj->where('status_id', $filterSearch->status_id);
            }
            if(@$filterSearch->category_id){
                $dataObj = $dataObj->where('category_id', $filterSearch->category_id);
            }
            $dataObj = $dataObj->orderBy('status_id', 'ASC')->orderBy('id', 'DESC')->with( 'created_by', 'company', 'location', 'brand', 'model','category', 'status', 'condition');
        }
    
        if($search){
            $dataObj = $dataObj->where(function($q) use($search){
                $capSearch = strtoupper($search);
                           
                    $q->where('title', 'like', '%'.$search.'%')
                    ->orWhereHas('asset', function ($qq) use($search) { 
                        $qq->where('asset_name', 'like', '%'.$search.'%')
                        ->orWhere('asset_code',  'like', '%'.$search.'%')
                        ->orWhere('serial_number',  'like', '%'.$search.'%')
                        ->orWhere('po_number',  'like', '%'.$search.'%');
                    });     
               
            });

            $dataObj = $dataObj->get();
          
            $dataArray['data'] = $dataObj->toArray();
        }else{
            $dataArray = $dataObj->paginate($paginate);
        }
       
        return response()->json($dataArray, 200);
    }
}
