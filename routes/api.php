<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Transaction\TransactionProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(["prefix" => "product"], function () {
    Route::get("", [ProductController::class, "index"]);
    Route::get("{id}", [ProductController::class, "show"]);
    Route::post("", [ProductController::class, "store"]);
    Route::put("{id}", [ProductController::class, "update"]);
    Route::delete("{id}", [ProductController::class, "destroy"]);
});

Route::group(["prefix" => "transaction"], function () {
    Route::get("", [TransactionProductController::class, "index"]);
    Route::get("{id}", [TransactionProductController::class, "show"]);
    Route::post("", [TransactionProductController::class, "store"]);
    Route::put("{id}", [TransactionProductController::class, "update"]);
    Route::delete("{id}", [TransactionProductController::class, "destroy"]);
});
