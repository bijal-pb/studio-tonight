<?php

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\StudioController;
use App\Http\Controllers\API\FavouriteController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\TypeController;
use App\Http\Controllers\API\AmenitiesController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\ReportController;
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

Route::post('login/google', [UserController::class, 'handleGoogle'])->middleware('apilogs');
Route::post('login/facebook', [UserController::class, 'handleFacebook']);
Route::post('login/apple', [UserController::class, 'handleApple']);
Route::get('app/version', [UserController::class, 'appVersion']);
Route::get('valid/coupon',[BookingController::class,'CouponCheck']);
Route::get('studio/search', [StudioController::class, 'studioSearch']);

Route::get('types', [TypeController::class, 'typeList']);
Route::get('amenities', [AmenitiesController::class, 'amenitiesList']);


Route::get('verification/list', [StudioController::class, 'verificationList']);

Route::group(['middleware' => ['apilogs','auth.optional']], function () {
    Route::get('customer/home', [StudioController::class, 'customerHome']);
    Route::get('customer/studios', [StudioController::class, 'studioList']);

    Route::get('studio/detail', [StudioController::class, 'studioDetail']);

});

Route::group(['middleware' => ['apilogs','auth:api']], function () {
    // Route::get('types', [TypeController::class, 'typeList']);
    // Route::get('amenities', [AmenitiesController::class, 'amenitiesList']);

    // studio routes
    Route::post('studio/add', [StudioController::class, 'studioAdd']);
    Route::post('studio/delete', [StudioController::class, 'studioDelete']);
    Route::get('studio/home', [StudioController::class, 'studioHome']);
    // Route::get('verification/list', [StudioController::class, 'verificationList']);
    Route::get('studio/calender', [BookingController::class, 'studioCalender']);
    Route::get('studio/booking/all', [BookingController::class, 'bookings']);
    Route::post('studio/booking/detail', [BookingController::class, 'bookingDetail']);

    // Route::get('studio/detail', [StudioController::class, 'studioDetail']);
    Route::get('get/studio', [StudioController::class, 'studios']);
    

    // customer routes
    // Route::get('customer/home', [StudioController::class, 'customerHome']);
    // Route::get('customer/studios', [StudioController::class, 'studioList']);
    Route::post('customer/review', [StudioController::class, 'review']);
    Route::get('customer/booking/all', [BookingController::class, 'customerBookings']);
    Route::get('studio/slots', [BookingController::class, 'availableSlots']);
    Route::post('customer/studio/book', [BookingController::class, 'studioBook']);
    Route::post('customer/transaction', [BookingController::class, 'customerTransaction']);
   

    //customer studio favourites

    Route::post('studio/favourite/add', [FavouriteController::class, 'favouriteAdd']);
    Route::get('customer/favourite/list', [FavouriteController::class, 'favouriteStudioList']);

    // profile
    Route::get('profile', [UserController::class, 'me']);
    Route::post('profile/edit', [UserController::class, 'edit_profile']);
    Route::post('profile/edit/image', [UserController::class, 'edit_profile_image']);
    Route::get('notification/enable', [UserController::class, 'notificationEnable']);
    Route::get('user/switch', [UserController::class, 'switchUser']);
    Route::post('change/password', [UserController::class, 'change_password']);

    //report 
    Route::post('customer/report', [ReportController::class, 'report']);

    //notification
    Route::get('notifications', [NotificationController::class,'notifications']);
    Route::get('read/notifications', [NotificationController::class,'read_notifications']);
   
    //logout
    Route::get('logout', [UserController::class, 'logout']);

    //version
    // Route::get('version', [UserController::class, 'appVersion']);

    //stripe
    Route::get('stripe/express/url', [UserController::class, 'stripe_express_url']);
    Route::get('stripe/account/status', [UserController::class, 'retrive_acc']);
});

Route::get('stripe/refresh/{id}', [UserController::class, 'stripe_refresh']);
Route::post('/intercept', [UserController::class, 'interceptLogin']);
