<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserApiController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SpecModelController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ApprovalSetupController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/sanctumlogin', [UserApiController::class, 'login']);
Route::post('/sanctumlogout', [UserApiController::class, 'logout']);

/**
 * Loggedin Routes
 */
Route::get('/fetch/log-profile/{ecode}/{token}', [ProfileController::class, 'fetchProfile'])->name('fetch.profile');

Route::middleware('authkey')->group(function () {
    // Account
    Route::prefix('account')->group(function () {
        // save
        Route::post('/save', [UserController::class, 'saveUser'])->name('account.save');
        // profile
        Route::get('/profile/{id}', [ProfileController::class, 'getProfileById'])->name('profile.get.by.id');
        Route::post('/profile/save', [ProfileController::class, 'saveProfile'])->name('profile.save');
        Route::post('/create-new/profile', [ProfileController::class, 'createNewProfile'])->name('profile.create.new');
    });

    /**
     * Users
     */
    Route::prefix('user')->group(function () {
        Route::get('/all', [UserController::class, 'getUsers'])->name('admin.get.all.users');
        Route::get('/fetch/signatories', [ProfileController::class, 'fetchSignatories'])->name('fetch.signatories');
    });

    Route::get('/admin/add-new/profile-by/ecode/{ecode}', [UserController::class, 'validateUser'])->name('profile.fetch.by.ecode');

    // fetch data
    Route::get('/brands/all', [BrandController::class, 'fetchData'])->name('admin.get.all.brands');
    Route::get('/models/all', [SpecModelController::class, 'fetchData'])->name('admin.get.all.models');
    Route::get('/categories/all', [CategoryController::class, 'fetchData'])->name('admin.get.all.categories');
    Route::get('/locations/all', [LocationController::class, 'fetchData'])->name('admin.get.all.locations');
    Route::get('/vendors/all', [VendorController::class, 'fetchData'])->name('admin.get.all.vendors');
    Route::get('/approval-setups/all/{type}', [ApprovalSetupController::class, 'fetchData'])->name('admin.get.all.approvals');

    // Store - Update data
    Route::get('/companies/all', [CompanyController::class, 'fetchDataObj'])->name('admin.get.all.companies');
    Route::post('/companies/store-update/data', [CompanyController::class, 'storeUpdate'])->name('admin.store.update.companies');
    Route::post('/companies/status-change/data', [CompanyController::class, 'statusChangeData'])->name('admin.status.change.companies');
    Route::get('/companies/status-change/data', [CompanyController::class, 'statusChangeData'])->name('admin.status.change.companies');
    Route::get('/company/state/list', [CompanyController::class, 'getCompanyList'])->name('admin.companies.list');


    Route::post('/locations/store-update/data', [LocationController::class, 'storeUpdate'])->name('admin.store.update.locations');
    Route::post('/locations/status-change/data', [LocationController::class, 'statusChangeData'])->name('admin.status.change.locations');
    Route::get('/fetch/locations/non-paginated/data', [LocationController::class, 'nonPaginatedData'])->name('admin.fetch.non.locations');

    Route::post('/brands/store-update/data', [BrandController::class, 'storeUpdate'])->name('admin.store.update.brands');
    Route::post('/brands/status-change/data', [BrandController::class, 'statusChangeData'])->name('admin.status.change.brands');
    Route::get('/fetch/brands/non-paginated/data', [BrandController::class, 'nonPaginatedData'])->name('admin.fetch.non.brands');

    Route::post('/categories/store-update/data', [CategoryController::class, 'storeUpdate'])->name('admin.store.update.categories');
    Route::post('/categories/status-change/data', [CategoryController::class, 'statusChangeData'])->name('admin.status.change.categories');
    Route::get('/fetch/categories/non-paginated/data', [CategoryController::class, 'nonPaginatedData'])->name('admin.fetch.non.paginate');

    Route::post('/models/store-update/data', [SpecModelController::class, 'storeUpdate'])->name('admin.store.update.models');
    Route::post('/models/status-change/data', [SpecModelController::class, 'statusChangeData'])->name('admin.status.change.models');
    Route::get('/fetch/models/non-paginated/data', [SpecModelController::class, 'nonPaginatedData'])->name('admin.fetch.non.models');

    Route::post('/vendors/store-update/data', [VendorController::class, 'storeUpdate'])->name('admin.store.update.vendors');
    Route::post('/vendors/status-change/data', [VendorController::class, 'statusChangeData'])->name('admin.status.change.vendors');
    Route::get('/fetch/vendors/non-paginated/data', [VendorController::class, 'nonPaginatedData'])->name('admin.fetch.non.vendors');

    Route::post('/approval-setups/store-update/data', [ApprovalSetupController::class, 'storeUpdate'])->name('admin.store.update.approvals');
    Route::post('/approval-setups/store-signatory/update-data', [ApprovalSetupController::class, 'storeSignatoriesUpdate'])->name('admin.store.update.signatories');
    Route::post('/approval-setups/status-change/data', [ApprovalSetupController::class, 'statusChangeData'])->name('admin.status.change.approvals');
    Route::get('/fetch/approval-setups/non-paginated/data/{type}', [ApprovalSetupController::class, 'nonPaginatedData'])->name('admin.fetch.non.approvals');
    Route::get('/fetch/approval-setups/single-data/{id}', [ApprovalSetupController::class, 'fetchDataByID'])->name('admin.fetch.by.id');


    Route::get('/fetch/profile-facility/team', [ProfileController::class, 'fetchFacilityTeam'])->name('fetch.facility.team');
    Route::get('/admin/add-new/profile-by/ecode/{ecode}', [UserController::class, 'validateUser'])->name('profile.fetch.by.ecode');
    Route::get('/admin/user/single/{id}', [ProfileController::class, 'getProfileById'])->name('profile.fetch.by.id');
    Route::get('/fetch/pages-slug', [PageController::class, 'fetchData'])->name('admin.fetch.slug.pages');
    Route::post('/store-page/settings', [PageController::class, 'storeUpdate'])->name('admin.storeUpdate');
    Route::post('/store-page/settings-capabilities/profile', [ProfileController::class, 'storePageCapabilities'])->name('profile.page.capabilities');

    Route::post('/store-update/setup-status', [StatusController::class, 'storeUpdate'])->name('admin.status.store.update');
    Route::get('/fetch-global/setup-status', [StatusController::class, 'fetchData'])->name('admin.fetch.status.data');
    Route::get('/fetch-global/incident-status/active', [StatusController::class, 'fetchIncidentStatus'])->name('admin.fetch.incident.status'); // incident status - dropdown
    Route::get('/fetch-global/incident-types/active', [StatusController::class, 'fetchIncidentTypes'])->name('admin.fetch.incident.types'); // incident types - dropdown

    Route::post('/store-update/notification', [NotificationController::class, 'storeUpdate'])->name('admin.notification.store.update');
    Route::get('/fetch/notification-setup', [NotificationController::class, 'fetchData'])->name('admin.notification.fetch');

    // Incident receivers upon new submit
    Route::get('/fetch/incident-receivers', [NotificationController::class, 'fetchIncidentReceivers'])->name('admin.notification.receivers.incident');

    Route::get('/fetch/asset-info/by/asset-code/{code}', [AssetController::class, 'fetchAssetCode'])->name('admin.asset.fetch.code');

    /**
     * Filepond
     */
    Route::post('/file/upload', [FileController::class, 'upload'])->name('file.upload');

    Route::get('/fetch/companies', [CompanyController::class, 'fetchData'])->name('admin.fetch.companies');

    Route::post('/store-update/incident', [IncidentController::class, 'storeUpdate'])->name('admin.storeUpdate.incident');
    Route::get('/fetch/incidents-by/users', [IncidentController::class, 'fetchData'])->name('admin.incident.fetch');
    Route::get('/fetch/incident/single-data/{id}', [IncidentController::class, 'fetchDataByID'])->name('admin.fetch.incident.by.id');
    Route::post('/delete/uploaded-file', [FileController::class, 'removeData'])->name('admin.remove.data.file');
});
    Route::post('/upload-files/docs-images', [FileController::class, 'storeData'])->name('admin.store.data.file');
    
require __DIR__.'/dev/api/jc.php';
require __DIR__.'/dev/api/ri.php';