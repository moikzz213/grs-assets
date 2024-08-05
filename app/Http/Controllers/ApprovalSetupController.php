<?php

namespace App\Http\Controllers;

use App\Helper\GlobalHelper;
use App\Models\RequestAsset;
use Illuminate\Http\Request;
use App\Models\ApprovalSetup;
use App\Models\ApprovalStage;
use App\Models\RequestApproval;

class ApprovalSetupController extends Controller
{
    public function nonPaginatedData($type){
        $query = ApprovalSetup::where('type', '=',$type)->whereIn('status', ['active', 'Active'])->orderBy('title', 'ASC')->get();

        return response()->json($query, 200);
    }

    /**
     * 
     * /api/fetch/approval-setups/single-data/{id}
     * 
     * */ 
    public function fetchDataByID($id){
        $query = ApprovalSetup::where('id', $id)->with('stages', function($q) {
            $q->orderBy('sort', 'ASC');
            $q->with('signatures', function($qq){
                $qq->select(['profile_id']);
            });
        })->first(); 
        return response()->json($query, 200);
    }

    public function fetchDataByIDRequestAsset(Request $request, $id){
       $isEdit = $request->input('isEdit');

        $query = ApprovalSetup::where('id', $id)->with('stages', function($q) use($isEdit){
            $q->orderBy('sort', 'ASC');
            $q->with('signatures', function($qq) use($isEdit){
                if(!$isEdit){
                    $qq->whereNot('status','inactive');
                }
            });
        })->first(); 
        return response()->json($query, 200);
    }

    public function fetchData(Request $request){
        $paginate = $request->show; 

        $sort = "";
        $orderBy = $request['sort'];
        $type = $request['type'];

        $dataObj = new ApprovalSetup;
        
        if($orderBy){
            $orderBy = json_decode($orderBy);
            $field = $orderBy[0];
            $sort = $orderBy[1];
            $dataObj = $dataObj->where('type', $type)->orderBy($field, $sort)->with('profile');
        }else{
            $dataObj = $dataObj->where('type', $type)->orderBy('status', 'ASC')->orderBy('title', 'ASC')->with('profile');
        }
       
        $dataArray = $dataObj->paginate($paginate); 
       
        return response()->json($dataArray, 200);
    }

    public function storeUpdate(Request $request){
        
        if($request->id){
            $query = ApprovalSetup::where('id', $request->id)->first();
            $query->update(array('title' => $request->title,'profile_id' => $request->profile_id, 'enable_attachment' => $request->enable_attachment));
            $message = 'Data has been updated';
            $log_type = 'update';
            $ID = $request->id;
        }else{
            $query = ApprovalSetup::create(array('title' => $request->title,'profile_id' => $request->profile_id, 'type' => $request->type, 
            'enable_attachment' => $request->enable_attachment));
            $message = 'Data has been created';
            $log_type = 'new';
            $ID = $query->id;
        }

        $helper = new GlobalHelper;
        $helper->createLogs($query, $request->profile_id, $log_type, $query);

        return response()->json(array('message' => $message, 'id' => $ID), 200);
    }

    public function storeSignatoriesUpdate(Request $request){
        if($request->id){ 
            
            if($request->class == 'new'){
                $query = ApprovalStage::create(array('approval_setup_id' => $request->id,'sort' => $request->sort));
                $query->signatures()->attach([0]);
                $message = 'Signatory has been Added';
                $log_type = 'new';
                $ID = $query->id;

            }elseif($request->class == 'update'){
                $query = ApprovalStage::where('id',$request->signatories['id'])->first();
                $query->update(array('types' => $request->signatories['types'],'sort' => $request->signatories['sort']));
              
                $query->signatures()->sync($request->signatories['signatories']);
                
                $message = 'Signatory has been updated';
                $log_type = 'update';
                $ID = $request->signatories['id'];
            }else{
                $query = ApprovalStage::where('id',$request->stageID)->first();
                $query->signatures()->sync([]);
                $query->delete();
              
                
                $message = 'Signatory has been deleted';
                $log_type = 'deleted';
                $ID = $request->stageID;
            }      

            $helper = new GlobalHelper;
            $helper->createLogs($query, $request->profile_id, $log_type, $query);

            return response()->json(array('message' => $message, 'id' => $ID), 200);
        }
    }

    public function statusChangeData(Request $request){

        $query = ApprovalSetup::where('id', $request->id)->first();
        if($request->status == 'disabled'){ 
            $status = 'disabled';
            
        }else{
            $status = 'active';
        }
        $log_type = 'change-status';
        
        $message = 'Data has been '.$status;
        $query->update(array('status' => $status,'profile_id' => $request->profile_id));

        $helper = new GlobalHelper;
        $helper->createLogs($query, $request->profile_id, $log_type, $query);

        return response()->json(array('message' => $message), 200);
    }

    public function changeSignatory(Request $request){
        if($request['serial']){
            $serial_no = explode("SN-",$request['serial']);
            $serial_no = substr($serial_no[1], 1); 
            $serial_no = (int) $serial_no;
           
            $query = RequestApproval::where('request_asset_id', $serial_no)->where('profile_id', $request['old'])
            ->where(function($q){
                $q->where('status', 'awaiting-approval')
                ->orWhere('status', 'pending')
                ->orWhere('status', 'reject');
            })->update(['profile_id' => $request['new']]);

            RequestAsset::where('id', $serial_no)->where('reminder_profile_id', $request['old'])->update(['reminder_profile_id' => $request['new']]);
        }else{ 
       
            RequestApproval::where('profile_id', $request['old'])
                        ->where(function($q){
                            $q->where('status', 'awaiting-approval')
                            ->orWhere('status', 'pending')
                            ->orWhere('status', 'reject');
                        })->update(['profile_id' => $request['new']]);

            RequestAsset::where('reminder_profile_id', $request['old'])
            ->where(function($q){
                $q->where('status', 'awaiting-approval')
                ->orWhere('status', 'pending')
                ->orWhere('status', 'reject');
            })->update(['reminder_profile_id' => $request['new']]);
        }
            $message = 'Approver has been changed successfully.';
        return response()->json(array('message' => $message), 200);
    }
}
