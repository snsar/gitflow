<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function (Request $request) {
    return $request->all();
});
Route::get('/vulnerable', [TestController::class, 'vulnerable']);
Route::get('/safe', [TestController::class, 'safe']);
Route::get('/payload', [TestController::class, 'createPayload']);
Route::get('/search', [TestController::class, 'unsafeSearch']);
Route::get('/upload-vulnerable', [FileController::class, 'uploadVulnerable']);

