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
use App\Models\AllottedInformation;
use App\Models\FinancialInformation;


class AssetController extends Controller
{

    public function import(Request $request) {
        $resMsg = "";
        $resCode = 200;
        $assetArray = array();
        $warrantyArray = array();
        $itemsToImport = json_decode($request['import_data']);
        $importCount = 0;

        DB::beginTransaction();
        try {
            $importAsset = new Asset;
            $importWarranty = new Warranty;

            // chunk data to 1000
            foreach (array_chunk($itemsToImport, 1000) as $itemsToImport_chunked){

                // loop each asset from chunked data (1000)
                foreach ($itemsToImport_chunked as $item) {

                    // check if the asset exist
                    $check = Asset::where('asset_code', $item->asset_code)->first();

                    if(!$check){
                        // insert multiple assets
                        // push fields into assetArray
                        // array_push($assetArray, array(
                        //     // asset
                        //     'asset_name' => $item->asset_name,  // asset_name
                        //     'asset_code' => $item->asset_code, // asset_code
                        //     'brand' => $item->brand, // brand
                        //     'model' => $item->model, // model
                        //     'specification' => $item->specification, // specification
                        //     'serial_number' => $item->serial_number, // serial_number
                        //     'company_id' => (int)$item->business_unit, // business_unit should be id
                        //     'category_id' => (int)$item->category, // category should be id
                        //     'location_id' => (int)$item->location, // location should be id
                        //     'status_id' => (int)$item->status, // status
                        //     'condition_id' => (int)$item->condition, // condition should be id

                        //     // purchase info
                        //     'vendor_id' => (int)$item->vendor, // amc_vendor
                        //     'po_number' => $item->po_number, // po_number
                        //     'purchased_date' => $item->purchased_date, // purchased_date
                        //     'price' => (float)$item->price, // price
                        //     'remarks' => $item->remarks, // remarks
                        // ));
                        // $assetLastInsertedId = $importAsset->insertGetId($assetArray);

                        // insert asset and get the id
                        $assetLastInsertedId = Asset::insertGetId([
                           // asset
                           'asset_name' => $item->asset_name,  // asset_name
                           'asset_code' => $item->asset_code, // asset_code
                           'brand' => $item->brand, // brand
                           'model' => $item->model, // model
                           'specification' => $item->specification, // specification
                           'serial_number' => $item->serial_number, // serial_number
                           'company_id' => (int)$item->business_unit, // business_unit should be id
                           'category_id' => (int)$item->category, // category should be id
                           'location_id' => (int)$item->location, // location should be id
                           'status_id' => (int)$item->status, // status
                           'condition_id' => (int)$item->condition, // condition should be id

                           // purchase info
                           'vendor_id' => (int)$item->vendor, // amc_vendor
                           'po_number' => $item->po_number, // po_number
                           'purchased_date' => $item->purchased_date, // purchased_date
                           'price' => (float)$item->price, // price
                           'remarks' => $item->remarks, // remarks
                        ]);

                        if($assetLastInsertedId){
                            // create warranty if amc_vendor
                            if($item->amc_vendor){
                                $createWarranty = Warranty::create([
                                    'warranty_title' => $item->warranty_title, // amc_end_date
                                    'warranty_start_date' => $item->warranty_start_date, // amc_end_date
                                    'warranty_end_date' => $item->warranty_end_date, // amc_end_date
                                    'amc_end_date' => $item->amc_end_date, // amc_end_date
                                    'amc_start_date' => $item->amc_start_date, // amc_start_date
                                    'vendor_id' => $item->amc_vendor, // amc_vendor should be id
                                    'asset_id' => $assetLastInsertedId,
                                ]);
                            }

                            // create financial information if any of the fields are not empty
                            if(
                                $item->capitalization_price ||
                                $item->end_of_life ||
                                $item->capitalization_date ||
                                $item->depreciation_percentage ||
                                $item->scrap_value ||
                                $item->scrap_date
                            ){
                                $createFinancialInfo = FinancialInformation::create([
                                    'capitalization_price' => (float)$item->capitalization_price, // capitalization_price
                                    'end_of_life' => $item->end_of_life, // end_of_life
                                    'capitalization_date' => $item->capitalization_date, // capitalization_date
                                    'depreciation_percentage' => $item->depreciation_percentage, // depreciation_percentage
                                    'scrap_value' => $item->scrap_value, // scrap_value
                                    'scrap_date' => $item->scrap_date, // scrap_date
                                    'asset_id' => $assetLastInsertedId,
                                ]);
                            }

                            // create allotted information if allotted_to is not empty
                            // if(@$item->alloted_to){
                            //     $allotedInformation = AllottedInformation::create([
                            //         'asset_id' => $assetLastInsertedId, // asset id
                            //         'location_id' => (int)$item->alloted_to, // allotted location id
                            //         'type' => 'allotted', // allotted type
                            //         'remarks' => '', // allotted info remarks
                            //     ]);
                            // }

                            $importCount++;

                        }
                    }
                }
            }
            $resMsg = 'Asset imported successfully ('.$importCount.')';
            $resCode = 200;
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $resCode = 500;
            $resMsg = "Import failed ". $e;
        }
        return response()->json([
            'message' => $resMsg
        ], $resCode);
    }

    public function getAssetById($id) {
        $asset = Asset::where('id', $id)
        ->with(
            'category',
            'company',
            'location',
            'financial_information',
            'pivot_warranties.vendor',
            'pivot_warranties.attachment',
            'allotted_informations.location',
            'maintenance.profile',
            'maintenance.status',
            'maintenance.handled_by',
            'attachments',
            'incidents.urgency',
            'incidents.status',
            'incidents.type',
            'incidents.handled_by',
            'incidents.handled_by',
            'incidents.profile',
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
                'model' => $request['model'],
                'brand' => $request['brand'],
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

            $warranty = Asset::find($request['asset_id']);
            if($request['id']){
                $warranty->pivot_warranties()->detach($request['old_warranty_id']);
                $warranty->pivot_warranties()->detach($request['warranty_id']);
            }else{
                $warranty->pivot_warranties()->detach($request['warranty_id']);
            }

            $warranty->pivot_warranties()->attach($request['warranty_id']);
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

    function searchAssets($search){
        $query = Asset::where('asset_name', 'LIKE', '%'.$search.'%')->orWhere('asset_code', 'LIKE', '%'.$search.'%')
        ->orderBy('asset_name', 'ASC')
        ->get();

        return response()->json($query, 200);
    }

    function getWarrantyByAssetId($assetId) {
        $warranties = Asset::where('id', '=',$assetId)
        ->with( 'pivot_warranties.vendor')
        ->orderBy('id', 'DESC')
        ->get();


        if($warranties && count($warranties) > 0){
            $warranties = $warranties[0]->pivot_warranties;
        }
        return response()->json($warranties, 200);
    }

    public function fetchAssetCode($code){
        $query = Asset::where('asset_code', '=',$code)->with('warranties.vendor', 'incidents.status', 'incidents.type', 'incidents.remarks','category','company','location')->first();
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
            $dataObj = $dataObj->orderBy($field, $sort)->with( 'created_by', 'company', 'location', 'category', 'status', 'condition');
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
            $dataObj = $dataObj->orderBy('status_id', 'ASC')->orderBy('id', 'DESC')->with( 'created_by', 'company', 'location','category', 'status', 'condition');
        }

        if($search){
            $dataObj = $dataObj->where(function($q) use($search){
                $capSearch = strtoupper($search);

                    $q->where('asset_name', 'like', '%'.$search.'%')
                        ->orWhere('asset_code',  'like', '%'.$search.'%')
                        ->orWhere('serial_number',  'like', '%'.$search.'%'); 
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

    public function downloadAsset(){
        $query = Asset::with(
            'warranty_latest',
            'warranties.vendor',
            'allotted_informations.location',
            'allotted_information_latest',
            'financial_information',
            'maintenance',
            'category',
            'company',
            'location',
            'vendor',
            'condition',
            'status'
        )->get();
        return response()->json($query, 200);
    }

}
