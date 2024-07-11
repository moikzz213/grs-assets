<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Profile;
use App\Jobs\RejectMailJob;
use App\Helper\GlobalHelper;
use App\Models\RequestAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Jobs\NotifyApproverJob;
use App\Models\RequestApproval;
use App\Jobs\ApprovalsCompleted;
use App\Jobs\RequestTransferJob;
use App\Models\RequestAssetDetail;
use Illuminate\Support\Facades\DB;
use App\Models\AllottedInformation;

class RequestAssetController extends Controller
{
    public function fetchData(Request $request, $page){
        $paginate = $request->show;
        $search = $request->search;
        $ID = $request->userid;
        $role = $request->role;

        $sort = "";
        $orderBy = $request->sort;
        $filter = $request->filter;
        $filterSearch = json_decode($filter);
      
        $dataObj = new RequestAsset;
        $dataObj = $dataObj->where('types','=', $page);
        if($role != 'admin' && $role != 'superadmin' && $role != 'asset-supervisor' && $role != 'commercial-manager' && $role != 'facility' ){
            $dataObj = $dataObj->where(function($q) use($ID){
                $q->where('profile_id','=', $ID)->orWhereHas('request_approvals', function($q) use($ID){
                    $q->where('profile_id', $ID)
                    ->where('status','=', 'awaiting-approval');
                });
            }); 
        } 

        if(@$filterSearch->company_id){
            $dataObj = $dataObj->where('company_id', $filterSearch->company_id);
        }

        if(@$filterSearch->location_id){
            $dataObj = $dataObj->where('transferred_to', $filterSearch->location_id);
        }

        if(@$filterSearch->from){
            $dataObj = $dataObj->where('transferred_from', $filterSearch->from);
        }

        if(@$filterSearch->status){
            $dataObj = $dataObj->where('status', $filterSearch->status);
        }
       
        if($orderBy){
            $orderBy = json_decode($orderBy);
            $field = $orderBy[0];
            $sort = $orderBy[1];
            
        }else{
       
            $field = 'updated_at';
            $sort = 'DESC';
        }

        $dataObj = $dataObj->orderBy(DB::raw("FIELD(reminder_profile_id,$ID)"), 'DESC')->orderBy($field, $sort)->with('items.assets', 'profile', 'company', 'transfer_to','transfer_from', 'reminder_profile');
         
        if($search){

            $dataObj = $dataObj->where(function($q) use($search){
                $capSearch = strtoupper($search);
                $checking = explode("SN-5", $capSearch);
                $checking2 = explode("SN-3", $capSearch);

                if(count($checking) > 1){
                    $searchID = (int)end($checking);
                    $q->where('id', '=', $searchID);
                }elseif(count($checking2) > 1){
                    $searchID = (int)end($checking2);
                    $q->where('id', '=', $searchID);
                }else{
                    $q->where('subject', 'like', '%'.$search.'%');
                }
            });

            $dataObj = $dataObj->get();
            $dataArray['data'] = $dataObj->toArray();
        }else{
            $dataArray = $dataObj->paginate($paginate);
        }

        return response()->json($dataArray, 200);
    }

    public function fetchDataByID($id){
        $query = RequestAsset::where('id', $id)->with('items.assets', 'items.attachment','request_approvals.profile', 'profile', 'company',  'transfer_to','attachment')->first();
        return response()->json($query, 200);
    }

    public function storeUpdate(Request $request){
        if(!$request->profile_id || !$request->assets || !$request->approval){
            return response()->json(array('error' => "Asset Item or Approval is required! kindly refresh the page and add."), 200);
        }
        
        $role = $request->role;
        $assetApprovals = array();
        $jobData = array();
        $firstReminder = 0;
        foreach($request->approval AS $k => $v){
            $status = 'pending';
            if($k == 0){
                $status = 'awaiting-approval';
                $jobData = array('profile_id' => $v['profile_id']);
                $firstReminder = $v['profile_id'];
            }
            $assetApprovals[] = array(
                'profile_id' => $v['profile_id'],
                'approval_type' => $v['types'],
                'orders' => $k,
                'status' => $status
            );
        }

        $reminderDate = Carbon::now()->addDay(1);

        $dataArr = array(
            'company_id' => $request->data['company_id'],
            'transferred_from' => $request->data['transferred_from'],
            'transferred_to' => $request->data['transferred_to'],
            'subject' => $request->data['subject'],
            'types' => $request->type,  // request or transfer
            'reminder_date' => $reminderDate,
            'reminder_profile_id' => $firstReminder
        );

        $jobData = array_merge($jobData, array('type' => $request->type, 'subject' => $request->data['subject']));

        if(@$request->data['id']){
            $ID = $request->data['id'];
            $query = RequestAsset::where('id','=', $ID)->first();
            if($query->status !== 'pending' && @$request->data['requestor_id'] == $request->profile_id){
                return response()->json(array('error' => "This request has already been signed, you cannot update anymore.", 'id' => $ID), 200);
            }
            $query->update($dataArr);
            if(count($request->assets) > 0 ){
                $query->items()->delete(); 
            }
            if(count($assetApprovals) > 1 ){
                $query->request_approvals()->delete();
            }
            $message = 'Request has been updated.';
            $log_type = 'update';
        }else{
            if($request->type == 'request'){
                $assetApprovals[] = array(
                    'profile_id' => $request->profile_id,
                    'approval_type' => 'receiver',
                    'orders' => count($assetApprovals),
                    'status' => 'pending'
                );
            }

            $dataArr = array_merge($dataArr, array(
                'request_type_id' => $request->data['request_type_id'],
                'profile_id' => $request->profile_id
            ));
            $query = RequestAsset::create($dataArr);
            $ID = $query->id;
            $message = 'Request has been submitted.';
            $log_type = 'new';

            $jobData = array_merge($jobData, array('id' => $ID, 'order' => 0));
            RequestTransferJob::dispatchAfterResponse(['data' => json_encode($jobData)])->onQueue('default');
        }
        $query->request_approvals()->createMany($assetApprovals);
        $query->items()->createMany($request->assets);
         
        if($request->data['extra_attachment'] == 1){
            $query->attachment()->sync($request['additionalFiles']); 

        }

        $helper = new GlobalHelper;
        $helper->createLogs($query, $request->profile_id, $log_type, $query);
        return response()->json(array('message' => $message, 'id' => $ID), 200);
    }

    public function changeRequest(Request $request){
        if(!$request->profile_id){
            return;
        }
        $query = RequestAsset::where('id','=', $request->ID)->first();
        if($request->status == 'cancelled'){ 
            $query->update(array('status' => $request->status, 'reminder_date' => null, 'reminder_profile_id' => null));
        }else{
            $getApprover = RequestApproval::where(array('request_asset_id' => $request->ID, 'status' => 'awaiting-approval'))->first();
            $query->update(array('status' => $request->status, 'reminder_date' => Carbon::now()->addDay(1), 'reminder_profile_id' => $getApprover->profile_id));
        }

        $helper = new GlobalHelper;
        $helper->createLogs($query, $request->profile_id, 'update-status', $query);

        $message = 'Request has been '.$request->status;
        return response()->json(array('message' => $message, 'id' => $request->ID), 200);
    }

    public function publicApproveSignatory(Request $request){
        $ID = (int) $request->id;
        $profile = $request->profile_id;
        $order = $request->order;
        $types = $request->type;
        $is_reject = $request->is_reject;
        $requestorID = $request->requestor_id;

        $queryRequest = RequestAsset::where('id','=', $ID)->where(function($q){
            $q->whereNot('status','=','complete')->orWhereNot('status','=','cancelled');
        })->with('items', function($q) {
            $q->whereNotNull('asset_code');
        })->first();

        if(!$queryRequest){
            return response()->json(array('message' => 'This request has been cancelled.', 'success' => false), 200);
        }

        $query2 = RequestApproval::where(['request_asset_id' => $ID, 'profile_id' => $profile, 'orders' => $order])
        ->where(function($q) {
            $q->where('status','=', 'awaiting-approval')
            ->orWhere('status','=', 'reject');
        } )->first();
        if(!$query2){
            return response()->json(array('message' => 'You already approved this request.', 'success' => false), 200);
        }

        $newOrder = (int)$order + 1;

        if($is_reject){
            $query3 = RequestApproval::where(['request_asset_id' => $ID, 'orders' => $order])->first();
            $message = 'Request has been rejected';
            $queryRequest->update(array('status' => 'reject', 'reason_rejected' => $is_reject, 'reminder_date' => null, 'reminder_profile_id' => null));
            $query3->update(array('status' => 'reject', 'reason_rejected' => $is_reject));

            // Notify requestor - request has been rejected

            $jobData = array('typeReceiver' => 'requestor','profile_id' => $requestorID, 'type' => $types, 'subject' => $queryRequest->subject, 'id' => $ID, 'order' => $order);
            RejectMailJob::dispatchAfterResponse(['data' => json_encode($jobData)])->onQueue('default');
        }else{
            $query3 = RequestApproval::where(['request_asset_id' => $ID, 'orders' => $newOrder])->first();
            $query2->update(array('status' => 'done','date_approved' => Carbon::now()));

            if($query3){
                $jobData = array( 'profile_id' => $query3->profile_id, 'type' => $types, 'subject' => $queryRequest->subject, 'id' => $ID, 'order' => $newOrder);
                $query3->update(array('status' => 'awaiting-approval'));
                RequestTransferJob::dispatchAfterResponse(['data' => json_encode($jobData)])->onQueue('default');

                $stats = 'awaiting-approval';
            }else{
                $stats = 'complete';
            }

            // Notify next approver

            $selfJobData = array('typeReceiver' => 'success', 'profile_id' => $profile, 'type' => $types, 'subject' => $queryRequest->subject, 'id' => $ID);
            if($stats != 'complete'){
                NotifyApproverJob::dispatchAfterResponse(['data' => json_encode($selfJobData)])->onQueue('default');
            }

            if(count($request->assets) > 0) {
                RequestAssetDetail::upsert(
                    $request->assets
                , ['id','request_asset_id'], ['is_available','item_description', 'qty','asset_code', 'weight', 'item_value', 'uom', 'remarks', 'remarks_commercial', 'remarks_receive', 'remarks_release','remarks_transport','is_received']);


                if($stats == 'complete'){
                    $updateData = array('status' => $stats, 'is_available' => 1, 'reminder_date' => null, 'reminder_profile_id' => null,'date_closed' => Carbon::now()); 

                }else{
                    $updateData = array('status' => $stats, 'is_available' => 1, 'reminder_date' => Carbon::now()->addDay(1), 'reminder_profile_id' => $query3->profile_id);
                }

               $queryRequest->update($updateData);

            }else{

                $updateData = array('status' => $stats, 'is_available' => 1, 'reminder_date' => Carbon::now()->addDay(1), 'reminder_profile_id' => @$query3->profile_id);
 
                $queryRequest->update($updateData);
            }

            // Approval completed 
               
            if($stats == 'complete'){
                $updateData = array('status' => $stats, 'date_closed' => Carbon::now(), 'reminder_date' => null, 'reminder_profile_id' => null);

                $pluckAssetCodes = array();
                $pluckAssetRemarks = array();
                if($queryRequest['items'] && count($queryRequest['items']) > 0){
                    foreach ($queryRequest['items'] as $key => $value) {
                        $assCode = trim($value['asset_code']);
                        $explodeAsset = explode(",", $assCode);
                        if(count($explodeAsset) > 1){
                            foreach($explodeAsset AS $zt => $vt){ 
                                $pluckAssetCodes[] = trim($vt);
                                $pluckAssetRemarks[] = array('asset_code' => $vt, 'remarks' => "ID:".$ID. " - ".$value['reason_for_request']);
                            }
                        }else{
                            $explodeAsset = explode("&", $assCode);

                            if(count($explodeAsset) > 1){
                                foreach($explodeAsset AS $zt => $vt){ 
                                    $pluckAssetCodes[] = trim($vt);
                                    $pluckAssetRemarks[] = array('asset_code' => $vt, 'remarks' => "ID:".$ID. " - ".$value['reason_for_request']);
                                }
                            }else{
                                $pluckAssetCodes[] = $value['asset_code'];
                                $pluckAssetRemarks[] = array('asset_code' => $value['asset_code'], 'remarks' => "ID:".$ID. " - ".$value['reason_for_request']);
                            } 
                           
                        } 
                    }
                }
              
                if(count($pluckAssetCodes) > 0){

                    $updateAsset = Asset::whereIn('asset_code', $pluckAssetCodes)->get();
                  
                    if(count($updateAsset)> 0){
                        $getAssetIds = array();
                        foreach ($updateAsset as $k => $v) {
                            $updateData = array('location_id' => $queryRequest->transferred_to);

                            if($queryRequest->transferred_to === 45){
                                $updateData = array('location_id' => $queryRequest->transferred_to, 'status_id' => 4);
                            }
                            $v->update($updateData);

                            //if($pluckAssetCodes[$k] == $v->asset_code){
                                $getAssetIds[] = array('asset_id' => $v->id, 'location_id' => $queryRequest->transferred_from,
                                'created_at' => Carbon::now(), 'remarks' => $pluckAssetRemarks[$k]['remarks']);
                           // }
                        }
                        
                        AllottedInformation::insert($getAssetIds);
                    }
                }  
                
                // Notify everyone once completed
                $query4 = RequestApproval::where(['request_asset_id' => $ID])->pluck('profile_id');
                ApprovalsCompleted::dispatchAfterResponse(['data' => json_encode($query4), 'id' => $ID, 'type' => $types])->onQueue('default');
            }


            $message = 'Request has been approved';
        }

        return response()->json(array('message' => $message, 'success' => true), 200);
    }

    public function fetchAssetLPOOnly(Request $request){
        $query = Asset::select('po_number')->where('po_number', '<>', '')->groupBy('po_number')->pluck('po_number');
        
        return response()->json(array('result' => $query), 200);
    }

    public function publicFetchRequest(Request $request){
            $query = RequestApproval::where(['request_asset_id' => $request->id])->first();
            if(!$query){
                return response()->json(array('access' => false, 'data' => null), 200);
            }

            //if($query->status == 'awaiting-approval' || $query->status == 'reject'){
                $query = RequestAsset::where('id', $request->id)->with('items.assets', 'items.attachment','setup','request_approvals.profile', 'profile', 'company', 'transfer_to.attachment',  'transfer_from.attachment', 'attachment')->first();

                return response()->json(array('access' => true, 'data' => $query), 200);
           // }
          //  return response()->json(array('access' => false, 'data' => null), 200);
    }


    public function transferApproval(Request $request){
        if($request->type == 'all'){
            $query = RequestApproval::where(['profile_id' => $request->from])->whereIn('status', ['pending', 'awaiting-approval'])->update(['profile_id' => $request->to]);
            RequestAsset::where(['reminder_profile_id' => $request->from])->whereIn('status', ['pending', 'awaiting-approval'])->update(['reminder_profile_id' => $request->to]);
        }else{
            $query = RequestApproval::where(['profile_id' => $request->from, 'approval_type' => $request->type])->whereIn('status', ['pending', 'awaiting-approval'])->get();
            if($query && count($query) > 0){
                foreach ($query as $key => $value) { 
                    RequestAsset::where(['id' => $value->request_asset_id])->whereIn('status', ['pending', 'awaiting-approval'])->update(['reminder_profile_id' => $request->to]);
                }
            }
            
            $query = RequestApproval::where(['profile_id' => $request->from, 'approval_type' => $request->type])->whereIn('status', ['pending', 'awaiting-approval'])->update(['profile_id' => $request->to]);
        }

        
        return response()->json(array('success' => true, 'data' => $query), 200);
    }
}
