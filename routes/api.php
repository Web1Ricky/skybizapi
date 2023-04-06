<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\SalesController;
use App\Http\Controllers\api\HomeController;
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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::middleware('VerifyToken')->group(function () {
    Route::middleware('ScopeApi:CusInv')->group(function () {
        Route::get('/sales', [SalesController::class,'ShowAll']);
        Route::get('/sales/detail', [SalesController::class,'SalesDetail']);
    });
    Route::middleware('ScopeApi:SO')->group(function () {
        Route::get('/sales_order', [SalesController::class,'SalesOrder']);
        Route::get('/sales_order/detail', [SalesController::class,'SODetail']);
    });
});
//Route::get('/tokendenied', [HomeController::class, 'AccessTokenDenied'])->name('tokendenied');
Route::get('/scopedenied', [HomeController::class, 'ScopeDenied'])->name('scopedenied');
Route::get('/sales/test', [SalesController::class,'APITest']);
Route::get('/hapus/cache', function(){Artisan::call('config:clear');});
