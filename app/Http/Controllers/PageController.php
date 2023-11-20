<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('layouts.app');
    }

    public function fetchData(){
        $query = Page::orderBy('title', 'ASC')->get(); 
        return response()->json($query, 200);
    }

    public function storeUpdate(Request $request){
        Page::truncate();
        $query = Page::insert($request['data']);

        Log::create(array('details' => json_encode($request['data']), 'profile_id' => $request->author, 'log_type' => 'setup-pages')); 
        return response()->json(array('message' => 'Data has been saved successfully!'), 200);
    }
}
