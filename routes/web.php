<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GisController;

Route::get('/', function () {
    return view('welcome');
});

// Route untuk Halaman Utama Peta dan Endpoint API GeoJSON
Route::get('/', [GisController::class, 'index']);
Route::get('/api/geojson/gadm/{level}', [GisController::class, 'getGadmGeoJson'])->where('level', '[0-4]');