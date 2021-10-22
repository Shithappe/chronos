<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\MailController;

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


Route::get('/awd', [UserController::class, 'main']);
Route::post('/awd', [UserController::class, 'main_post']);
Route::get('/user/{id}', [UserController::class, 'show']);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot_password', [MailController::class, 'email_forgot_password']);
Route::post('/reset_password', [AuthController::class, 'reset_pass']);
Route::get('/accepted_to_calendar/{calendar_id}/{user_id}', [RuleController::class, 'accept_rule']);


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user/search/{name}', [UserController::class, 'search']);
    
    Route::post('/new_calendar', [CalendarController::class, 'create']);
    Route::post('/new_event', [EventController::class, 'create']);
    Route::get('/calendar/{calendar_id}/events', [EventController::class, 'index']);
    Route::post('/new_rule', [RuleController::class, 'create']);
    Route::get('/user_calendars', [CalendarController::class, 'show_my_caledars']);
    Route::post('/share_calendar', [CalendarController::class, 'share_calendar']);

    Route::delete('/destroy_calendar/{id}', [CalendarController::class, 'destroy']);
    Route::delete('/destroy_event/{id}', [EventController::class, 'destroy']);
    Route::patch('/event/{id}', [EventController::class, 'update']);
    Route::patch('/update_calendar_member', [RuleController::class, 'update_calendar_member']);
    Route::delete('/destroy_calendar_member/{calendar_id}/{user_id}', [RuleController::class, 'destroy_calendar_member']);

    Route::post('/reset_password/{token}', [AuthController::class, 'reset_password']);
});