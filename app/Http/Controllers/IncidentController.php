<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Helper\GlobalHelper;
use App\Jobs\IncidentReport;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function fetchData(Request $request){
        $paginate = $request->show;
        $search = $request->search;
        $ID = $request->id;
        $role = $request->role;

        $sort = "";
        $orderBy = $request->sort;
        $filter = $request->filter;
        $filterSearch = json_decode($filter);
       
        $dataObj = new Incident;
        if($role != 'admin' && $role != 'superadmin' && $role != 'technical-operation'){
            $dataObj = $dataObj->where('profile_id','=', $ID)->orWhere('handled_by','=', $ID);
        }
        if($orderBy){
            $orderBy = json_decode($orderBy);
            $field = $orderBy[0];
            $sort = $orderBy[1];
            $dataObj = $dataObj->orderBy($field, $sort)->with('asset', 'profile', 'company', 'location', 'type', 'status');
        }else{
            if(@$filterSearch->company_id){
                $dataObj = $dataObj->where('company_id', $filterSearch->company_id);
            }
            if(@$filterSearch->location_id){
                $dataObj = $dataObj->where('location_id', $filterSearch->location_id);
            }
            if(@$filterSearch->type_id){
                $dataObj = $dataObj->where('type_id', $filterSearch->type_id);
            }
            if(@$filterSearch->status_id){
                $dataObj = $dataObj->where('status_id', $filterSearch->status_id);
            }
            $dataObj = $dataObj->orderBy('status_id', 'ASC')->orderBy('title', 'ASC')->with('asset', 'profile', 'company', 'location', 'type', 'status');
        }
    
        if($search){
            $dataObj->where(function($q) use($search){
                $capSearch = strtoupper($search);
                $checking = explode("ISR-", $capSearch);
                
                if(count($checking) > 1){
                    $searchID = (int)end($checking); 
                    $q->where('id', '=', $searchID);
                }else{
                    
                    $q->where('title', 'like', '%'.$search.'%')
                    ->orWhereHas('asset', function ($qq) use($search) { 
                        $qq->where('asset_name', 'like', '%'.$search.'%')
                        ->orWhere('asset_code', '=', $search);
                    });     
                } 
            });

            $dataObj = $dataObj->get();
            $dataArray['data'] = $dataObj->toArray();
        }else{
            $dataArray = $dataObj->paginate($paginate);
        }
       
        return response()->json($dataArray, 200);
    }

    public function storeUpdate(Request $request){
     
        if($request->id){
            $query = Incident::where('id', $request->id)->first();
            $dataForm = array(
                'asset_id' => @$request->asset_id, 
                'profile_id' => $request->profile_id,
                'title' => $request->title,
                'description' => $request->description, 
                'company_id' => $request->company_id,  
                'location_id' => $request->location_id, 
                'type_id' => $request->type_id,  
                'urgency' => $request->urgency,   
            );

            $query->update($dataForm);
            $message = 'Incident has been updated';
            $log_type = 'update';
            $ID = $request->id;
        }else{
            $dataForm = array(
                'asset_id' => $request->asset_id, 
                'profile_id' => $request->profile_id,
                'title' => $request->title,
                'description' => $request->description, 
                'company_id' => $request->company_id,  
                'location_id' => $request->location_id, 
                'type_id' => $request->type_id,  
                'urgency' => $request->urgency,  
                'status_id' => 7
            );
            $query = Incident::create( $dataForm );

            IncidentReport::dispatchAfterResponse(['data' => json_encode($dataForm)])->onQueue('default'); 

            $message = 'Incident has been reported';
            $log_type = 'new';
            $ID = $query->id;
        }

        if(request()->file('filename')){
            // upload image
            $fileArray = array();
            $uploadDate = Carbon::now()->format('YmdHis');
            $files = Collection::wrap(request()->file('file'));
            
            $userStorage = '/uploads';
            if (!Storage::exists($userStorage)) {
                Storage::makeDirectory($userStorage, 0755, true);
            }

            $files->each(function ($file, $key) use (&$userStorage, &$fileArray, &$uploadDate) {
                $userStorageDir = storage_path() . '/app' . $userStorage;
                $fileName = $file->getClientOriginalName();
                $title = pathinfo($fileName, PATHINFO_FILENAME);
                $extn = strtolower($file->getClientOriginalExtension());
                $slugTitle = Str::slug($title, '-');
                $path = $slugTitle."-".$uploadDate.".".$extn;
                $mime = $file->getClientMimeType();

                if($extn == 'pdf' || $extn == 'PDF'){
                    $file->move($userStorageDir, $path);
                }else{
                // File Optimization
                $img = Img::make($file);
                $img->encode($extn, 50);

                // Save file to storage directory
                $img->save($userStorageDir . '/' . $path);
                }
              
                // Setup data into array
                array_push( $fileArray, array(
                    'original_name' => $fileName,
                    'title' => $title,
                    'disk' => 'local',
                    'path' => $path, 
                    'types' => 'request',
                    'mime' => $mime,
                    'user_id' => auth()->id(),
                    'created_at' => Carbon::now(),
                ));
            }); 

            // Recursive create
            $idsToSync = array();
            foreach ($fileArray as $singleFile) {
                $imgId = DB::table('images')->insertGetId($singleFile);
                array_push($idsToSync, $imgId);
            }

            if(count($idsToSync) > 0){
                $data->images()->attach($idsToSync);
            } 
        }

        $helper = new GlobalHelper;
        $helper->createLogs($query, $request->profile_id, $log_type, $query);

        return response()->json(array('message' => $message, 'id' => $ID), 200);
    }

    public function updateIncidentFacilityTeam(Request $request){
        if($request->id){
             
            $query = Incident::where('id', $request->id)->first();
           
            $query->update(array(
                'priority' => $request->priority,
                'handled_by' => $request->handled_by,
                'status_id' => $request->status_id,
                'remarks'   => $request->remarks
            ));

            $helper = new GlobalHelper;
            $helper->createLogs($query, $request->profile_id, 'incident-facility', $query);

            return response()->json(array('message' => 'Incident has been updated.'), 200);
        }

        return false;
    }

    public function fetchDataByID($id){
        $query = Incident::where('id', $id)->with('asset', 'profile', 'company', 'location', 'type', 'status','files')->first(); 
        return response()->json($query, 200);
        
    }
}
