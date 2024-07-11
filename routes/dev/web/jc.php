<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;


Route::get('/pv/employee-signatory/{type}/approvals', [PageController::class, 'home'])->name('public.approvals');
Route::get('/pv/employee-signatory/{type}/approvals/{page}', [PageController::class, 'home'])->name('public.approvals');
Route::get('/link/reset-password/employee-ecode', [PageController::class, 'home'])->name('reset-password-link');
?>