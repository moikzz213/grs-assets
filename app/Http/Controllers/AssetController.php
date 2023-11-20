<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Incident;
use App\Models\Warranty;
use App\Helper\GlobalHelper;
use App\Models\RequestAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class AssetController extends Controller
{
    public function getAssetById($id) {
        $asset = Asset::where('id', $id)
        ->with(
            'brand',
            'model',
            'category',
            'company',
            'location',
            'financial_information',
            'warranty.vendor',
            'allotted_informations',
            'maintenance.profile',
            'maintenance.status', 
            'maintenance.handled_by',
            'attachments'
        )->first();
        return response()->json($asset, 200);
    }

    public function save(Request $request) {

        $globalHelper = new GlobalHelper;
        $msg = "";
        $statusCode = 200;
        $assetArray = array();
        $financialArray = array();
        $asset = null;

        DB::beginTransaction();
        try {
            $assetArray = array(
                'asset_name' => $request['asset_name'],
                'serial_number' => $request['serial_number'],
                'section_code' => $request['section_code'],
                'category_id' => $request['category_id'],
                'company_id' => $request['company_id'],
                'location_id' => $request['location_id'],
                'status_id' => $request['status_id'], // asset status
                'author_id' => $globalHelper->client_auth()->id,

                // specs
                'specification' => $request['specification'],
                'model_id' => $request['model_id'],
                'brand_id' => $request['brand_id'],
                'condition_id' => $request['condition_id'],

                // purchase
                'price' => isset($request['price']) ? (float)$request['price'] : null, // or number_format($request['price'], 2, '.', '')
                'vendor_id' => isset($request['vendor_id']) ? $request['vendor_id'] : null,
                'po_number' => isset($request['po_number']) ? $request['po_number'] : null,
                'purchased_date' => isset($request['purchased_date']) ?  Carbon::parse($request['purchased_date']) : null,

                // edit fields
                'remarks' => isset($request['remarks']) ? $request['remarks'] : null,
                'last_author_id' => $globalHelper->client_auth()->id,

            );

            if(isset($request['id'])){
                // get the asset
                $asset = Asset::find($request['id']);

                // update
                $asset->update($assetArray);

                // save financial information
                $asset->financial_information()->updateOrCreate(
                    ['id' => $request['financial_information_id']],
                    [
                        'capitalization_price' => isset($request['capitalization_price']) ? (float)$request['capitalization_price'] : null,
                        'depreciation_percentage' => isset($request['depreciation_percentage']) ? $request['depreciation_percentage'] : null,
                        'scrap_value' => isset($request['scrap_value']) ? (float)$request['scrap_value'] : null,
                        'scrap_date' => isset($request['scrap_date']) ? Carbon::parse($request['scrap_date']) : null,
                        'capitalization_date' => isset($request['capitalization_date']) ? Carbon::parse($request['capitalization_date']) : null,
                        'end_of_life' => isset($request['end_of_life']) ? Carbon::parse($request['end_of_life']) : null,
                    ]
                );

                // sync files
                $asset->attachments()->sync($request['file_ids']);
            }else{
                $asset = Asset::create($assetArray);
                $generatedAssetCode =  $request['company_code'].'-'.$request['category_code'].'-'. str_pad($asset->id, 5, '0', STR_PAD_LEFT);
                $asset->asset_code = strtoupper($generatedAssetCode);
                $asset->save();
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

    function saveAssetWarranty(Request $request) {
        $globalHelper = new GlobalHelper;
        $msg = "";
        $statusCode = 200;
        $assetWarrantyArray = array();
        $financialArray = array();
        $warranty = null;

        DB::beginTransaction();
        try {
            $assetWarrantyArray = array(
                'title' => $request['warranty_title'],
                'warranty_start_date' => $request['warranty_start_date'],
                'warranty_end_date' => $request['warranty_end_date'],
                'vendor_start_date' => $request['vendor_start_date'],
                'vendor_end_date' => $request['vendor_end_date'],
                'asset_id' => $request['asset_id'],
                'vendor_id' => $request['warranty_vendor_id'],
            );

            if(isset($request['id'])){
                // get the warranty
                $warranty = Warranty::find($request['id']);
                // update
                $warranty->update($assetWarrantyArray);

            }else{
                $asset = Warranty::create($assetWarrantyArray);
            }

            DB::commit();
            $msg = "Warranty has been saved ";
        } catch (Exception $e) {
            DB::rollback();
            $statusCode = 500;
            $msg = "Error while saving Warranty";
        }

        return response()->json([
            'warranty' => $warranty,
            'message' => $msg,
        ], $statusCode);
    }

    function getWarrantyByAssetId($assetId) {
        $warranties = Warranty::where('asset_id', '=',$assetId)
        ->with('vendor')
        ->orderBy('id', 'DESC')
        ->get();
        return response()->json($warranties, 200);
    }

    public function fetchAssetCode($code){
        $query = Asset::where('asset_code', '=',$code)->with('warranty.vendor', 'incidents.status', 'incidents.type', 'incidents.remarks','brand','model','category','company','location')->first();
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


    /**
     * Below is for Dashboard
     */
    public function dashboardData(Request $request){
        $incidents = Incident::whereNot('status_id', 2)->whereNot('status_id', 8)->count();
        $maintenance = Incident::where('type_id',2)->whereNot('status_id', 8)->count();
        $req = RequestAsset::where('types','=', 'request')->whereNot('status', 'complete')->count();
        $transfer = RequestAsset::where('types','transfer')->whereNot('status', 'complete')->count();

        
        $query_all_asset    = Asset::orderBy('updated_at', 'DESC')->with('category','company','location', 'created_by','status')->limit(10)->get();
        $query_all_incident = Incident::orderBy('updated_at', 'DESC')->with('company','location','profile', 'asset.category','status')->limit(10)->get();
        $query_all_request = RequestAsset::orderBy('updated_at', 'DESC')->with('company','profile','transfer_to')->limit(10)->get();

        $newArray = array();
        

        if(count($query_all_asset) > 0){
            foreach ($query_all_asset as $k => $v) {
                $newArray[] = array(
                    'company' => $v->company ? $v->company['title'] : '',
                    'location' => $v->location ? $v->location['title'] : '',
                    'category' => $v->category ? $v->category['title'] : '',
                    'asset_name' => $v->asset_name,
                    'asset_code' => $v->asset_code,
                    'type' => $v->status ? "asset(".$v->status['title'].')' : '',
                    'author' => $v->created_by ? $v->created_by['display_name'] : '',
                    'date' => date('d/m/Y', strtotime($v->updated_at))
                );
            }
        }

        $newArray2 = array();
        if(count($query_all_incident) > 0){
            foreach ($query_all_incident as $k => $v) {
                $newArray2[] = array(
                    'company' => $v->company ? $v->company['title'] : '',
                    'location' => $v->location ? $v->location['title'] : '',
                    'category' => $v->asset ? $v->asset['category']['title'] : '',
                    'asset_name' => $v->asset ? $v->asset['asset_name'] : $v->title,
                    'asset_code' => $v->asset ? $v->asset['asset_code'] : $v->asset_code,
                    'type' => $v->status ? "incident(".$v->status['title'].')' : '',
                    'author' => $v->profile ? $v->profile['display_name'] : '',
                    'date' => date('d/m/Y', strtotime($v->updated_at))
                );
            }
        }

        $newArray3 = array();
        if(count($query_all_request) > 0){
            foreach ($query_all_request as $k => $v) {
                $newArray3[] = array(
                    'company' => $v->company ? $v->company['title'] : '',
                    'location' => $v->transfer_to ? $v->transfer_to['title'] : '',
                    'category' => '',
                    'asset_name' => '',
                    'asset_code' => '',
                    'type' => $v->types."(".$v->status.')',
                    'author' => $v->profile ? $v->profile['display_name'] : '',
                    'date' => date('d/m/Y', strtotime($v->updated_at))
                );
            }
        }

        $merge_data = array_merge($newArray, $newArray2, $newArray3);
        
        usort($merge_data, function ($a, $b) {
            return strtotime($b['date']) -strtotime($a['date']);
        });
        
        $merge_data = array_slice($merge_data, 0, 10);
       
        $response = array('count' => array('incident' => $incidents, 'maintenance' => $maintenance, 'request' => $req, 'transfer' => $transfer),
                          'table' => $merge_data
                    );
    
        return response()->json($response, 200);
    }
}