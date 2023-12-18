<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SpecModelController;

Route::middleware('authkey')->group(function () {
    // categories
    Route::get('/category/state/category-list', [CategoryController::class, 'getCategoryList'])->name('admin.category.list');
    // locations
    Route::get('/location/state/location-list', [LocationController::class, 'getLocationList'])->name('admin.location.list');
    // brands
    Route::get('/brand/state/brand-list', [BrandController::class, 'getBrandList'])->name('admin.brand.list');
    // models
    Route::get('/model/state/model-list', [SpecModelController::class, 'getModelList'])->name('admin.model.list');
    // vendor
    Route::get('/vendor/state/vendor-list', [VendorController::class, 'getVendorList'])->name('admin.vendor.list');
    // statuses
    Route::get('/status/state/status-list', [StatusController::class, 'getStatusList'])->name('admin.status.list');
    Route::post('/status/state/status-update', [StatusController::class, 'updateStatusState'])->name('admin.status.update');
    Route::post('/status/state/save-list', [StatusController::class, 'saveStatusList'])->name('admin.status.update');

    // assets
    Route::post('/asset/save', [AssetController::class, 'save'])->name('admin.asset.save');
    Route::post('/asset/import', [AssetController::class, 'import'])->name('admin.asset.import');
    Route::post('/asset/save-warranty', [AssetController::class, 'saveAssetWarranty'])->name('admin.asset.warranty.save');
    Route::get('/asset/warranty/{assetId}', [AssetController::class, 'getWarrantyByAssetId'])->name('admin.asset.warranty.save');
    Route::get('/asset/{id}', [AssetController::class, 'getAssetById'])->name('admin.asset.single');

    // files
    Route::get('/system/file/all/{type?}', [FileController::class, 'getPaginatedFiles'])->name('admin.files.paginated');
    Route::post('/system/file/upload', [FileController::class, 'upload'])->name('admin.file.upload');
});
