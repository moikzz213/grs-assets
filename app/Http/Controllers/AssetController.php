<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{

    function getAssetById($id) {
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

        $msg = "";
        $statusCode = 200;
        $assetArray = array();
        $asset = null;

        DB::beginTransaction();
        try {
            $assetArray = array(
                'company_id' => $request['company_id'],
                'location_id' => $request['location_id'],
                'category_id' => $request['category_id'],
                'status_id' => $request['status_id'],
                'brand_id' => $request['brand_id'],
                'model_id' => $request['model_id'],
                'vendor_id' => $request['vendor_id'],
                'author_id' => $request['author_id'],
                'asset_name' => $request['asset_name'],
                'asset_code' => $request['asset_code'],
                'serial_number' => $request['serial_number'],
                'section_code' => $request['section_code'],
                'specification' => $request['specification'],
                'price' => $request['price'],
                'po_number' => $request['po_number'],
                'purchased_date' => $request['purchased_date'],
                'remarks' => $request['remarks']
            );

            if(isset($request['id']) && $request['id'] !== null){

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
            'message' => $msg,
        ], $statusCode);
    }

    public function fetchAssetCode($code){
        $query = Asset::where('asset_code', '=',$code)->with('warranty.vendor', 'maintenance','brand','model','category','company','location')->first();
        return response()->json($query, 200);
    }
}
