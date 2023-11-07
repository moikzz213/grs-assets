<?php 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncidentController;

Route::middleware('authkey')->group(function () {
    Route::post('/incident/update-facility-team', [IncidentController::class, 'updateIncidentFacilityTeam'])->name('incident.update.facility.team');
});

?>