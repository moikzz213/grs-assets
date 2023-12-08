<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Incident;
use App\Helper\GlobalHelper;
use App\Jobs\IncidentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class IncidentController extends Controller
{
    public function fetchData(Request $request){
        $paginate = $request->show;
        $search = $request->search;
        $ID = $request->userid;
        $role = $request->role;

        $sort = "";
        $orderBy = $request->sort;
        $filter = $request->filter;
        $filterSearch = json_decode($filter);

        $dataObj = new Incident;

        if($role !== 'admin' && $role !== 'superadmin' && $role !== 'facility' && $role !== 'technical-operation' && $role !== 'asset-supervisor'){
            $dataObj = $dataObj->where('profile_id','=', $ID)->orWhere('handled_by','=', $ID);
        }

        $dataObj = $dataObj->whereNot('type_id', 2); // where not type maintenance

        if($orderBy){
            $orderBy = json_decode($orderBy);
            $field = $orderBy[0];
            $sort = $orderBy[1];
            $dataObj = $dataObj->orderBy($field, $sort)->with('asset', 'profile', 'company', 'location', 'type', 'status','urgency');
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
            $dataObj = $dataObj->orderBy('status_id', 'ASC')->orderBy('id', 'DESC')->with('asset', 'profile', 'company', 'location', 'type', 'status','urgency');
        }

        if($search){
            $dataObj = $dataObj->where(function($q) use($search){
                $capSearch = strtoupper($search);
                $checking = explode("ISR-2", $capSearch);

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

    public function fetchMaintenanceData(Request $request){
        $paginate = $request->show;
        $search = $request->search;

        $sort = "";
        $orderBy = $request->sort;
        $filter = $request->filter;
        $filterSearch = json_decode($filter);

        $dataObj = new Incident;
        $dataObj = $dataObj->where('type_id', 2);
        if($orderBy){
            $orderBy = json_decode($orderBy);
            $field = $orderBy[0];
            $sort = $orderBy[1];
            $dataObj = $dataObj->orderBy($field, $sort)->with('asset', 'profile', 'company', 'location', 'type', 'status', 'handled_by', 'urgency');
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
            $dataObj = $dataObj->orderBy('status_id', 'ASC')->orderBy('id', 'DESC')->with('asset', 'profile', 'company', 'location', 'type', 'status', 'handled_by', 'urgency');
        }

        if($search){
            $dataObj = $dataObj->where(function($q) use($search){
                $capSearch = strtoupper($search);
                $checking = explode("ISR-2", $capSearch);

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

        $assetID = @$request->asset_id;

        if(!$assetID && $request->asset_code){
            $fetchAsset = Asset::where('asset_code', '=', $request->asset_code)->first();
            if($fetchAsset){
            $assetID = $fetchAsset->id;
            }
        }

        if($request->id){
            $query = Incident::where('id', $request->id)->first();
            $dataForm = array(
                'asset_id' => $assetID, 
                'title' => $request->title,
                'description' => $request->description,
                'company_id' => $request->company_id,
                'location_id' => $request->location_id,
                'type_id' => $request->type_id,
                'urgency_id' => $request->urgency_id,
                'asset_code' => @$request->asset_code,
                'reminder_date' => Carbon::now()->addDay(1),
            );

            $query->update($dataForm);
            if($request->type_id == 2){
                $message = 'Maintenance has been updated';
            }else{
                $message = 'Incident has been updated';
            }
            $log_type = 'update';
            $ID = $request->id;
        }else{
            $dataForm = array(
                'asset_id' => $assetID,
                'profile_id' => $request->profile_id,
                'title' => $request->title,
                'description' => $request->description,
                'company_id' => $request->company_id,
                'location_id' => $request->location_id,
                'type_id' => $request->type_id,
                'urgency_id' => $request->urgency_id,
                'asset_code' => @$request->asset_code,
                'reminder_date' => Carbon::now()->addDay(1),
                'status_id' => 7
            );
            $query = Incident::create( $dataForm );

            $ID = $query->id;

            $dataForm = array_merge(array('id' => $ID), $dataForm);
            IncidentReport::dispatchAfterResponse(['data' => json_encode($dataForm)])->onQueue('default');
            if($request->type_id == 2){
                $message = 'Asset Maintenance has been created';

                Asset::where(['id' => $assetID])->update(['status_id' => 2]);
            }else{
                $message = 'Incident has been reported';
            }
            $log_type = 'new';

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
                'status_id' => $request->status_id
            ));

            if($request->remarks_data){
                $query->remarks()->create(['remarks' => $request->remarks_data, 'profile_id' => $request->profile_id]);
            }

            if($request->handled_by !== $request->prev_handled_by){
                $dataForm = array(
                    'id'            => $request->id,
                    'asset_id'      => @$request->asset_id,
                    'profile_id'    => $request->profile_id,
                    'title'         => $request->title,
                    'description'   => $request->description,
                    'company_id'    => $request->company_id,
                    'location_id'   => $request->location_id,
                    'type_id'       => $request->type_id,
                    'urgency_id'    => $request->urgency_id,
                    'asset_code'    => @$request->asset_code,
                    'reminder_date' => Carbon::now()->addDay(1),
                    'status_id'     => $request->status_id,
                    'handled_by'     => $request->handled_by,
                );
                
                IncidentReport::dispatchAfterResponse(['data' => json_encode($dataForm)])->onQueue('default');
            }

            $helper = new GlobalHelper;
            $helper->createLogs($query, $request->profile_id, 'incident-facility', $query);

            return response()->json(array('message' => 'Incident has been updated.'), 200);
        }

        return false;
    }

    public function fetchDataByID($id){
        $query = Incident::where('id', $id)
        ->with(
            'asset.pivot_warranties.vendor',
            'profile',
            'company',
            'location',
            'type',
            'status',
            'attachment',
            'remarks.profile',
            'urgency'
        )->first();
        return response()->json($query, 200);

    }

    public function syncImages(Request $request) {
        $query = Incident::where('id', $request->id)->with('attachment')->first();
        $query->attachment()->sync($request['file_ids']);
        $log_type = 'delete';
        if(count($request['file_ids']) > 0){
            $log_type = 'add-update';
        }

        $helper = new GlobalHelper;
        $helper->createLogs($query, $request->profile_id, $log_type, $query);
        return response()->json($query, 200);
    }
}
