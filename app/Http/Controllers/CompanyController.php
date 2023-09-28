<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    
    public function fetchData(){
        $query = Company::orderBy('title', 'ASC')->get();

        return response()->json($query, 200);
    }
}