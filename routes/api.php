<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [App\Http\Controllers\api\AuthController::class, 'register']);
//API route for login user

Route::post('login', [App\Http\Controllers\api\AuthController::class, 'login']); 

Route::group(['middleware' => ['auth:sanctum']], function () {
   
    Route::get('/assigntasklist', [App\Http\Controllers\api\AuthController::class, 'assigntasklist']);

    Route::get('/assigntasktoday', [App\Http\Controllers\api\AuthController::class, 'assigntasktoday']);
    Route::get('/assigntasktomorrow', [App\Http\Controllers\api\AuthController::class, 'assigntasktomorrow']);
    Route::get('/assigntaskcurrentmonth', [App\Http\Controllers\api\AuthController::class, 'assigntaskcurrentmonth']);
    Route::post('/updatetodaystask/{ticketId}', [App\Http\Controllers\api\AuthController::class, 'updatetodaystask']);
    Route::get('/closedtasks', [App\Http\Controllers\api\AuthController::class, 'closedtasks']);
    Route::get('/managetasktoday', [App\Http\Controllers\api\AuthController::class, 'managetasktoday']);
    Route::post('/managetaskstatus/{ticketId}', [App\Http\Controllers\api\AuthController::class, 'managetaskstatus']);
    
    Route::get('/userdetails', [App\Http\Controllers\api\AuthController::class, 'userdetails']);
    // API route for logout user
    Route::post('/logout', [App\Http\Controllers\api\AuthController::class, 'logout']);

});

    //Nv Details
    Route::get('/nv-daily-summary', [App\Http\Controllers\api\NvDetailsController::class, 'NV_summary_list_api']);

