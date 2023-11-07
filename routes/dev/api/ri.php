<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\StatusController;
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
    // conditions
    Route::get('/model/state/condition-list', [StatusController::class, 'getConditionList'])->name('admin.model.list');
});
