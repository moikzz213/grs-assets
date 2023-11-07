<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function fetchAssetCode($code){
        $query = Asset::where('asset_code', '=',$code)->with('warranty.vendor', 'maintenance','brand','model','category','company','location')->first(); 
        return response()->json($query, 200);
    }
}
