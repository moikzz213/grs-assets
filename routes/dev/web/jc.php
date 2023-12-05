<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;


Route::get('/pv/employee-signatory/{type}/approvals', [PageController::class, 'home'])->name('public.approvals');
?>