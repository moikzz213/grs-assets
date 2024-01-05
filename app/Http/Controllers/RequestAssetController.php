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
        if($role != 'admin' && $role != 'superadmin' && $role != 'asset-supervisor' && $role != 'commercial-manager'){
            $dataObj = $dataObj->where('profile_id','=', $ID);
        }
        if($orderBy){
            $orderBy = json_decode($orderBy);
            $field = $orderBy[0];
            $sort = $orderBy[1];
            $dataObj = $dataObj->orderBy($field, $sort)->with('items.assets', 'profile', 'company', 'transfer_to');
        }else{
            if(@$filterSearch->company_id){
                $dataObj = $dataObj->where('company_id', $filterSearch->company_id);
            }
            if(@$filterSearch->location_id){
                $dataObj = $dataObj->where('transferred_to', $filterSearch->location_id);
            }

            if(@$filterSearch->status){
                $dataObj = $dataObj->where('status', $filterSearch->status);
            }
            $dataObj = $dataObj->orderBy('status', 'DESC')->orderBy('id', 'DESC')->with('items.assets', 'profile', 'company',  'transfer_to');
        }

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
        $query = RequestAsset::where('id', $id)->with('items.assets', 'items.attachment','request_approvals.profile', 'profile', 'company',  'transfer_to')->first();
        return response()->json($query, 200);
    }

    public function storeUpdate(Request $request){
        if(!$request->profile_id){
            return;
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
            $query->items()->delete();

            $query->request_approvals()->delete();


            $message = 'Request has been updated.';
            $log_type = 'update';
        }else{

            $assetApprovals[] = array(
                'profile_id' => $request->profile_id,
                'approval_type' => 'receiver',
                'orders' => count($assetApprovals),
                'status' => 'pending'
            );

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

            $query->update(array('status' => $request->status, 'reminder_date' => null));
        }else{
            
            $query->update(array('status' => $request->status, 'reminder_date' => Carbon::now()->addDay(1)));
        }

        $helper = new GlobalHelper;
        $helper->createLogs($query, $request->profile_id, 'update-status', $query);

        $message = 'Request has been '.$request->status;
        return response()->json(array('message' => $message, 'id' => $request->ID), 200);
    }

    public function publicApproveSignatory(Request $request){
        $ID = $request->id;
        $profile = $request->profile_id;
        $order = $request->order;
        $types = $request->type;
        $is_reject = $request->is_reject;
        $requestorID = $request->requestor_id;

        $query2 = RequestApproval::where(['request_asset_id' => $ID, 'profile_id' => $profile, 'orders' => $order])
        ->where(function($q) {
            $q->where('status','=', 'awaiting-approval')
            ->orWhere('status','=', 'reject');
        } )->first();
        if(!$query2){
            return response()->json(array('message' => 'You already approved this request.', 'success' => false), 200);
        }

        $newOrder = (int)$order + 1;

        $query = RequestAsset::where('id','=', $ID)->whereNot('status','=','complete')->with('items', function($q) {
            $q->whereNotNull('asset_code');
        })->first();
        if($is_reject){
            $query3 = RequestApproval::where(['request_asset_id' => $ID, 'orders' => $order])->first();
            $message = 'Request has been rejected';
            $query->update(array('status' => 'reject', 'reason_rejected' => $is_reject, 'reminder_date' => null));
            $query3->update(array('status' => 'reject', 'reason_rejected' => $is_reject));


            // Notify requestor - request has been rejected

            $jobData = array('typeReceiver' => 'requestor','profile_id' => $requestorID, 'type' => $types, 'subject' => $query->subject, 'id' => $ID, 'order' => $order);
            RejectMailJob::dispatchAfterResponse(['data' => json_encode($jobData)])->onQueue('default');
        }else{
            $query3 = RequestApproval::where(['request_asset_id' => $ID, 'orders' => $newOrder])->first();
            $query2->update(array('status' => 'done','date_approved' => Carbon::now()));

            if($query3){
                $jobData = array( 'profile_id' => $query3->profile_id, 'type' => $types, 'subject' => $query->subject, 'id' => $ID, 'order' => $newOrder);
                $query3->update(array('status' => 'awaiting-approval'));
                RequestTransferJob::dispatchAfterResponse(['data' => json_encode($jobData)])->onQueue('default');

                $stats = 'awaiting-approval';
            }else{
                $stats = 'complete';
            }

            // Notify next approver

            $selfJobData = array('typeReceiver' => 'success', 'profile_id' => $profile, 'type' => $types, 'subject' => $query->subject, 'id' => $ID);
            NotifyApproverJob::dispatchAfterResponse(['data' => json_encode($selfJobData)])->onQueue('default');

            if(count($request->assets) > 0) {
                RequestAssetDetail::upsert(
                    $request->assets
                , ['id','request_asset_id'], ['is_available', 'asset_code', 'weight', 'item_value', 'country_of_origin', 'remarks', 'remarks_commercial', 'remarks_receive', 'remarks_release','is_received']);


                if($stats == 'complete'){
                    $updateData = array('status' => $stats, 'is_available' => 1, 'reminder_date' => null, 'reminder_profile_id' => null,'date_closed' => Carbon::now());

                      // Notify everyone once completed
                      $query4 = RequestApproval::where(['request_asset_id' => $ID])->pluck('profile_id');
                      ApprovalsCompleted::dispatchAfterResponse(['data' => json_encode($query4), 'id' => $ID, 'type' => $types])->onQueue('default'); 

                }else{
                    $updateData = array('status' => $stats, 'is_available' => 1, 'reminder_date' => Carbon::now()->addDay(1), 'reminder_profile_id' => $query3->profile_id);
                }

                $query->update($updateData);

            }else{

                $updateData = array('status' => $stats, 'is_available' => 1, 'reminder_date' => Carbon::now()->addDay(1), 'reminder_profile_id' => @$query3->profile_id);

                // Approval completed

                if($stats == 'complete'){
                    $updateData = array('status' => $stats, 'date_closed' => Carbon::now(), 'reminder_date' => null, 'reminder_profile_id' => null);

                    $pluckAssetCodes = array();
                    $pluckAssetRemarks = array();
                    if($query['items'] && count($query['items']) > 0){
                        foreach ($query['items'] as $key => $value) {
                            $pluckAssetCodes[] = $value['asset_code'];
                            $pluckAssetRemarks[] = array('asset_code' => $value['asset_code'], 'remarks' => $value['reason_for_request']);
                        }
                    }
                    if(count($pluckAssetCodes) > 0){

                        $updateAsset = Asset::whereIn('asset_code', $pluckAssetCodes)->get();
                        if(count($updateAsset)> 0){
                            $getAssetIds = array();
                            foreach ($updateAsset as $k => $v) {
                                $v->update(array('location_id' => $query->transferred_to));

                                if($pluckAssetRemarks[$k] == $v->asset_code){
                                    $getAssetIds[] = array('asset_id' => $v->id, 'location_id' => $query->transferred_to,
                                    'created_at' => Carbon::now(), 'remarks' => $pluckAssetRemarks[$k]['remarks']);
                                }
                            }
                            AllottedInformation::insert($getAssetIds);
                        }
                    }

                    // Notify everyone once completed
                    $query4 = RequestApproval::where(['request_asset_id' => $ID])->pluck('profile_id');
                   
                }

                $query->update($updateData);
            }

            $message = 'Request has been approved';
        }

        return response()->json(array('message' => $message, 'success' => true), 200);
    }

    public function publicFetchRequest(Request $request){
            $query = RequestApproval::where(['request_asset_id' => $request->id, 'profile_id' => $request->profile_id])->first();
            if(!$query){
                return response()->json(array('access' => false, 'data' => null), 200);
            }

            //if($query->status == 'awaiting-approval' || $query->status == 'reject'){
                $query = RequestAsset::where('id', $request->id)->with('items.assets', 'items.attachment','setup','request_approvals.profile', 'profile', 'company', 'transfer_to',  'transfer_from')->first();

                return response()->json(array('access' => true, 'data' => $query), 200);
           // }
          //  return response()->json(array('access' => false, 'data' => null), 200);
    }
}
