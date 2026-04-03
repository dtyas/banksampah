<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NasabahController;

// Route::resource('nasabah', NasabahController::class);
Route::get('/', function () {
    return view('welcome');
});
