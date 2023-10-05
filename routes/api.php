<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserApiController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SpecModelController;

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
    });

    Route::get('/brands/all', [BrandController::class, 'fetchData'])->name('admin.get.all.brands');
    Route::get('/models/all', [SpecModelController::class, 'fetchData'])->name('admin.get.all.models');
    Route::get('/categories/all', [CategoryController::class, 'fetchData'])->name('admin.get.all.categories');
    Route::get('/companies/all', [CompanyController::class, 'fetchDataObj'])->name('admin.get.all.companies');
    Route::get('/locations/all', [LocationController::class, 'fetchData'])->name('admin.get.all.locations');
    Route::get('/vendors/all', [VendorController::class, 'fetchData'])->name('admin.get.all.vendors');
    
    Route::get('/admin/add-new/profile-by/ecode/{ecode}', [UserController::class, 'validateUser'])->name('profile.fetch.by.ecode');
    Route::get('/admin/user/single/{id}', [ProfileController::class, 'getProfileById'])->name('profile.fetch.by.id');
    Route::get('/fetch/pages-slug', [PageController::class, 'fetchData'])->name('admin.fetch.slug.pages');
    Route::post('/store-page/settings', [PageController::class, 'storeUpdate'])->name('admin.storeUpdate');
    Route::post('/store-page/settings-capabilities/profile', [ProfileController::class, 'storePageCapabilities'])->name('profile.page.capabilities');
});

Route::get('/fetch/companies', [CompanyController::class, 'fetchData'])->name('admin.fetch.companies');
