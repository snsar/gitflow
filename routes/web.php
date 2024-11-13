<?php

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/upload', [FileController::class, 'showUploadForm']);
Route::post('/upload', [FileController::class, 'uploadVulnerable']);

Route::get('/file/{filename}', [FileController::class, 'getFile']);
