<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\TBAdmitCardController;
use App\Http\Controllers\HomeController;
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
Route::get('/studentsdata/{ins}', [TBAdmitCardController::class, 'getData'])->name('ajaxstudentdata');

Route::get('/dataview/{tb}', [TBAdmitCardController::class, 'getexceldata'])->name('ajaxdataview');

Route::post('/return_response',[HomeController::class, 'bankResponse'])->name('return_response');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


