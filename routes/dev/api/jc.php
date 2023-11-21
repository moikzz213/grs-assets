<?php 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\CronJobController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\RequestAssetController;
use App\Http\Controllers\ApprovalSetupController;

Route::middleware('authkey')->group(function () {
    Route::post('/incident/update-facility-team', [IncidentController::class, 'updateIncidentFacilityTeam'])->name('incident.update.facility.team');
    Route::post('/incident/sync-images/store-update', [IncidentController::class, 'syncImages'])->name('change.request');

    Route::get('/fetch/request-assets/by-requestor/{page}', [RequestAssetController::class, 'fetchData'])->name('fetch.requestor.assets');
    Route::get('/fetch/request-assets/by-requestor/data/{id}', [RequestAssetController::class, 'fetchDataByID'])->name('fetch.requestor.assets.id');
    Route::post('/request-asset/store-update/data', [RequestAssetController::class, 'storeUpdate'])->name('request.asset-store.update');
    Route::post('/request-asset/change-request/data', [RequestAssetController::class, 'changeRequest'])->name('change.request');

    Route::get('/fetch/request-asset/approval-setup-fetch/{id}', [ApprovalSetupController::class, 'fetchDataByIDRequestAsset'])->name('admin.request.asset.fetch.by.id');

    Route::get('/fetch/maintenance-assets/data', [IncidentController::class, 'fetchMaintenanceData'])->name('admin.incident.fetch');
    Route::get('/fetch/maintenance-asset/single-data/{id}', [IncidentController::class, 'fetchMaintenanceDataByID'])->name('admin.fetch.incident.by.id');

    Route::get('/fetch/all-assets/data', [AssetController::class, 'fetchData'])->name('admin.fetch.data.assets');
    Route::get('/fetch-global/status/active/{type}', [StatusController::class, 'fetchStatusByType'])->name('admin.status.type');

    Route::get('/dashboard/fetch-query/data', [AssetController::class, 'dashboardData'])->name('admin.dashboard.data');
    
});

Route::prefix('public')->group(function () {
    Route::post('/fetch/request-assets/data', [RequestAssetController::class, 'publicFetchRequest'])->name('public.fetch.request.id');
    Route::post('/store/request-asset/approve-data', [RequestAssetController::class, 'publicApproveSignatory'])->name('public.approve.request');
    Route::get('/run/cron-jobs', [CronJobController::class, 'asset_notification'])->name('public.cron.job');
});
?>