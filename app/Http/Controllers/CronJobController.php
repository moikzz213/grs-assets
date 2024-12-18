<?php

namespace App\Http\Controllers;

use App\Jobs\TestJob;
use App\Models\Status;
use App\Models\Profile;
use App\Models\Incident;
use App\Models\Notification;
use App\Models\RequestAsset;
use Illuminate\Http\Request;
use App\Jobs\CronJobIncidents;
use Illuminate\Support\Carbon;
use App\Jobs\CronJobMaintenance;
use App\Jobs\CronJobAssetRequest;

class CronJobController extends Controller
{
    public function asset_notification(Request $request)
    {
        if(!$request->input('key') || !$request->input('c') || ( $request->input('key') != 'Moikzz' || $request->input('c') != 'Ghassan')){
            return json_encode(array("Message" => 'Forbidden access', 'Status Code' => 403));
        }

        $query = Notification::whereNotNull('meta_value')->get();

        $first_notification = 30;
        $second_notification = 15;
        $third_notification = 1;
        $request_notification = 1;
        $incident_notification = 1;

        $incidentReceiver = [];
        $maintenanceReceiver = [];

        if ($query && count($query) > 0) {
            foreach ($query as $k => $v) {
                if ($v['meta_type'] == 'first_notification') {
                    $first_notification = $v['meta_value']; // notification for asset expiration
                } elseif ($v['meta_type'] == 'second_notification') {
                    $second_notification = $v['meta_value']; // notification for asset expiration
                } elseif ($v['meta_type'] == 'third_notification') {
                    $third_notification = $v['meta_value']; // notification for asset expiration
                } elseif ($v['meta_type'] == 'request_transfer') {
                    $request_notification = $v['meta_value']; // notification for request - transfer
                } elseif ($v['meta_type'] == 'incidents') {
                   // $incident_notification = $v['meta_value']; // notification for incidents - maintenance
                } elseif ($v['meta_type'] == 'maintenance-receiver') {
                    $maintenanceReceiver[] = $v['meta_value']; // Email Receiver for maintenance
                } elseif ($v['meta_type'] == 'incident-receiver') {
                    $incidentReceiver[] = $v['meta_value']; // Email Receiver for incidents
                }
            }
        }

        /**
         * Maintenance
         */
        $this->maintenanceFn($maintenanceReceiver);

        /**
         * Incident
         */

        $this->incidentFn($incidentReceiver);

        /**
         * Request and Transfer Assets
         */
        $this->requestAssetFn();

       echo json_encode(array("message" => '403: Permission denied!'));
       return;
    }

    public function testJob(){
        TestJob::dispatchAfterResponse(['data' => 'test'])->onQueue('default');
        echo json_encode(array("message" => '403: Permission denied!'));
        return;
    }

    private function requestAssetFn(){
        $profilesWithAssetRequest = Profile::whereHas('reminder_profile', function($q){ 
            $q->whereHas('request_approvals', function($q){
                $q->where('status', 'awaiting-approval');
            });
        }) 
        ->with([
            'reminder_profile' => function($q){
                $q->whereNot('status', 'cancelled')
                ->whereDate('reminder_date', Carbon::now()->format('Y-m-d'))->with('request_approvals');
            },
        ])
        ->get();

        if ($profilesWithAssetRequest && count($profilesWithAssetRequest) > 0) {

            foreach ($profilesWithAssetRequest as $k => $profile) {

                if(count($profile->reminder_profile) > 0){ 
                    // run jobs to send email
                    CronJobAssetRequest::dispatchAfterResponse(['data' => $profile])->onQueue('default');
                    // update reminder date for all asset requests
                    $updateReminder = $profile->reminder_profile()->update(['reminder_date' => Carbon::now()->addDays(1)]);
                }
            }
        }
       return;
    }  

    private function maintenanceFn($maintenanceReceiver){

        $queryMaintenance = Incident::where('type_id', 2)->orWhere('type_id', 26)->orWhere('type_id', 27)
            ->whereDate('reminder_date', Carbon::now()->format('Y-m-d'))
            ->get();

        $dataSendToMaintenanceReceiver = [];
        $sendToHandler = [];

        if ($queryMaintenance && count($queryMaintenance) > 0) {

            foreach ($queryMaintenance as $k => $v) {

                if (@$v['handled_by']) {
                    if(!in_array($v['handled_by'], $sendToHandler)){
                        $sendToHandler[] = $v['handled_by'];
                    }
                } else {
                    $dataSendToMaintenanceReceiver[] = $v;
                }
                $addDays = Status::where('id', $v->urgency_id)->first();

                if($addDays){
                    $days = $addDays->notification_interval;
                }else{
                    $days = 1;
                }

                $v->update(['reminder_date' => Carbon::now()->addDays($days)]);
            }
            $queryProfile = null;

            if (count($sendToHandler) > 0) {

                /******
                 *   send to maintenance handler
                 *  */

                $queryProfile = Profile::whereIn('id', $sendToHandler)->pluck('email');

            }else{

                /******
                 *    send to maintenance default receiver
                 *    $maintenanceReceiver
                 *  */

                 $queryProfile = $maintenanceReceiver;

            }

            CronJobMaintenance::dispatchAfterResponse(['data' => json_encode($queryProfile)])->onQueue('default');

        }

        return;
    }

    private function incidentFn($incidentReceiver){
        $queryIncidents = Incident::where('type_id','!=', 2)->where('type_id','!=', 26)
            ->whereDate('reminder_date', Carbon::now()->format('Y-m-d'))
            ->get();

        $sendToIncidentReceiver = [];
        $sendToIncidentHandler = [];

        if ($queryIncidents && count($queryIncidents) > 0) {
            foreach ($queryIncidents as $k => $v) {
                if ($v['handled_by']) {
                    if(!in_array($v['handled_by'], $sendToIncidentHandler)){
                        $sendToIncidentHandler[] = $v['handled_by'];
                    }
                } else {
                    $sendToIncidentReceiver[] = $v;
                }

                $addDays = Status::where('id', $v->urgency_id)->first();
                if($addDays){
                    $days = $addDays->notification_interval;
                }else{
                    $days = 1;
                }

                $v->update(['reminder_date' => Carbon::now()->addDays($days)]);
            }

            $queryProfile = null;
            if (count($sendToIncidentHandler) > 0) {

                /******
                 *   send to maintenance handler
                 *  */

                $queryProfile = Profile::whereIn('id', $sendToIncidentHandler)->pluck('email');

            }else{

                /******
                 *    send to maintenance default receiver
                 *    $maintenanceReceiver
                 *  */

                 $queryProfile = $incidentReceiver;

            }

            CronJobIncidents::dispatchAfterResponse(['data' => json_encode($queryProfile)])->onQueue('default');
        }
        return;
    }
}