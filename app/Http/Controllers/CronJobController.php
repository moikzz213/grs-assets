<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CronJobController extends Controller
{
    public function asset_notification(Request $request)
    {
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
                    $incident_notification = $v['meta_value']; // notification for incidents - maintenance
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
        $this->maintenanceFn($maintenanceReceiver, $incident_notification);
        

        /**
         * Incident
         */
        
        $this->incidentFn($incidentReceiver, $incident_notification);    
       // dd($maintenanceReceiver);
    }

    private function maintenanceFn($maintenanceReceiver, $addDays){

        $queryMaintenance = Incident::where('type_id', 2)
            ->whereDate('reminder_date', Carbon::now()->format('Y-m-d'))
            ->get();

        $sendToMaintenanceReceiver = [];
        $sendToHandler = [];

        if (count($queryMaintenance) > 0) {
            foreach ($queryMaintenance as $k => $v) {
                if ($v['handled_by']) {
                    $sendToHandler[] = $v;
                    if(!in_array($v['handled_by'], $sendToHandler)){
                        $sendToHandler[] = $v['handled_by'];
                    }
                } else {
                    $sendToMaintenanceReceiver[] = $v;
                }
                $v->update(['reminder_date' => Carbon::now()->addDays($addDays)]);
            }

            if (
                count($sendToMaintenanceReceiver) > 0 &&
                count($maintenanceReceiver) > 0
            ) {
                // send to maintenance receiver here
            }

            if (count($sendToHandler) > 0) {
                // send to maintenance handler
            }
        }
    }

    private function incidentFn($incidentReceiver, $addDays){
        $queryIncidents = Incident::where('type_id','!=' , 2)
            ->whereDate('reminder_date', Carbon::now()->format('Y-m-d'))
            ->get();
 
        $sendToIncidentReceiver = [];
        $sendToIncidentHandler = [];

        if (count($queryIncidents) > 0) {
            foreach ($queryIncidents as $k => $v) {
                if ($v['handled_by']) {
                    if(!in_array($v['handled_by'], $sendToIncidentHandler)){
                        $sendToIncidentHandler[] = $v['handled_by'];
                    }
                } else {
                    $sendToIncidentReceiver[] = $v;
                }
               // $v->update(['reminder_date' => Carbon::now()->addDays($addDays)]);
            }

            if (
                count($sendToIncidentReceiver) > 0 &&
                count($incidentReceiver) > 0
            ) {
                // send to maintenance receiver here
            }
           
            if (count($sendToIncidentHandler) > 0) {
                // send to maintenance handler
            }
        }
    }
}