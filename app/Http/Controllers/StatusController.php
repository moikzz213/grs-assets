<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Helper\GlobalHelper;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function fetchData(){
        $query = Status::orderBy('status', 'ASC')->orderBy('type', 'ASC')->orderBy('title', 'ASC')->get();
        return response()->json($query, 200);
    }

    public function fetchIncidentStatus(){
        $query = Status::where('type', '=', 'incident')->orderBy('status', 'ASC')->orderBy('title', 'ASC')->get();
        return response()->json($query, 200);
    }

    public function fetchIncidentTypes(){
        $query = Status::where('type', '=', 'incident-type')->whereNot('id',"=",2)->orderBy('status', 'ASC')->orderBy('title', 'ASC')->get();
        //$query = Status::where('type', '=', 'incident-type')->orderBy('status', 'ASC')->orderBy('title', 'ASC')->get();
        return response()->json($query, 200);
    }

    public function fetchStatusByType($type){
        $query = Status::where('type', '=', 'asset')->orderBy('status', 'ASC')->orderBy('title', 'ASC')->get();
        return response()->json($query, 200);
    }

    public function storeUpdate(Request $request){
        $msg = 'Invalid Access';
        $code = 500;

        if($request->type == 'status'){
            $code = 200;
            $query = Status::where('id', $request->item['id'])->first();
            $status = 'active';
            if($request->item['status'] == 'active'){
                $status = 'disabled';
            }
            $query->update(['status' => $status]);

            $msg = 'Status has been '.$status;
            $log_type = 'change-status';
        }elseif($request->type == 'store-update'){
            $code = 200;
            if(count($request->update) > 0 ){
                Status::upsert(
                    $request->update
                , ['id'], ['title', 'type', 'profile_id']);

                $query = Status::orderBy('id', 'desc')->first();

                $log_type = 'multiple-update';
                $msg = 'Data has been saved.';
            }
            if(count($request->new) > 0 ){
                Status::insert($request->new);
                $msg = 'Data has been saved.';
            }
        }
        if(@$query){
            $helper = new GlobalHelper;
            $helper->createLogs($query, $request->profile_id, $log_type, $query);
        }
        return response()->json(array('message' => $msg), $code);
    }

    public function getConditionList() {
        $data = Status::where('status', 'active')->where('type', 'condition-type')->orderBy('title', 'ASC')->get();
        return response()->json($data, 200);
    }

    public function getStatusList() {
        $data = Status::orderBy('title', 'ASC')->get();
        return response()->json($data, 200);
    }

    public function updateStatusState(Request $request) {
        $helper = new GlobalHelper;
        // to add logs
        $updateStatus = Status::where('id', $request['id'])
        ->where('type', $request['type'])
        ->update([
            'status' => $request['status'],
            'profile_id' => $helper->client_auth()->id
        ]);
        return response()->json([
            'message' => 'Status has been updated'
        ], 200);
    }

    public function saveStatusList(Request $request) {

        // global helper
        $helper = new GlobalHelper;

        // loop through list
        $statusArray = array();
        foreach($request['list'] as $item){
            array_push($statusArray, [
                'id' => isset($item['id']) ? $item['id'] : null,
                'title' => $item['title'],
                'status' => $item['status'],
                'type' => $item['type'],
                'color' => $item['color'],
                // 'bgcolor' => $item['bgcolor'],
                'notification_interval' => $item['notification_interval'],
                'profile_id' => $helper->client_auth()->id
            ]);
        }

        // insert
        $updateStatusList = Status::upsert(
            $statusArray,
            ['id', 'title', 'type'],
        );

        // to add logs
        return response()->json([
            'message' => 'Status list has been updated'
        ], 200);
    }
}
