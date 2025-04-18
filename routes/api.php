<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RoverController;


# Reset all values to initial so needs to create "symbolic connection" again
Route::post('/reset', [RoverController::class, 'setReset']);

# Create "symbolic connection" with Rovert
Route::put('/connect', [RoverController::class, 'setConnect']);

# Send instructions to rover to turn/move forward
Route::post('/commands/{list}', [RoverController::class, 'setCommandsList']);
