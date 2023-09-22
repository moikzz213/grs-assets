<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\ClientKey;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ClientKeyController extends Controller
{ 

    public function removeKey(Request $request)
    {
        $clientKey = ClientKey::where([
            'key' => $request['key'],
        ])->delete();

        return response()->json([
            "message" => 'Key removed successfully',
            "key" => $clientKey
        ], 200);
    }
}
