<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

use App\Http\Controllers\API\V1\CompteController;

Route::prefix('v1')->middleware('auth:api')->group(function () {
    Route::get('/comptes', [CompteController::class, 'index']);
    // use implicit model binding for Compte (compte)
    Route::get('/comptes/{compte}', [CompteController::class, 'show'])->middleware('ensure.compte.access');
    Route::post('/comptes', [CompteController::class, 'store']);
    Route::patch('/comptes/{compte}', [CompteController::class, 'update'])->middleware('ensure.compte.access');
});
