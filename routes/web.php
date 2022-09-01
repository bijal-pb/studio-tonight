<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ChefController;
use App\Http\Controllers\Admin\LearnController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\StudioController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\TransactionController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/token/{id}', [HomeController::class, 'accessToken'])->name('authtoken');


// if (App::environment('production')) {
//     URL::forceScheme('https');
// }

// Route::get('google-login',[UserController::class, 'googleLogin']);
// Route::get('facebook-login',[UserController::class, 'facebookLogin']);
// Route::get('apple-login',[UserController::class, 'appleLogin']);

// Route::get('/privacy-policy',function () {
//     return view("admin.privacy-policy");
// })->name('privacy'); 
// Route::get('/terms',function () {
//     return view("admin.terms");
// })->name('terms');
Route::get('/', function () {
    return view("auth.login");
});
Route::post('/contact', [NotificationController::class ,'contact'])->name('contact');

Auth::routes();

Route::get('/home', function () {
    return redirect("/admin");
});

Route::post('admin/login', [UserController::class, 'admin_login'])->name('admin.login');

Route::name('admin.')->namespace('Admin')->group(function () {
    Route::group(['prefix' => 'admin', 'middleware' => ['admin.check']], function () {
        Route::get('/', [AdminController::class, 'index'])->name('home');
       
        // users  route
        Route::get('/profile', [UserController::class, 'profile'])->name('profile');
        Route::get('/password', [UserController::class, 'password'])->name('password');
        Route::post('/password/change', [UserController::class, 'change_password'])->name('password.update');
        Route::post('/profile/update', [UserController::class, 'update_profile'])->name('profile.update');
        Route::get('/users', [UserController::class, 'index'])->name('user');
        Route::get('/users/list', [UserController::class, 'users'])->name('users.list');
        Route::get('/get/user', [UserController::class, 'getUser'])->name('user.get');
        Route::get('/user/status/change', [UserController::class, 'changeStatus'])->name('user.status.change');
        Route::post('/user/store', [UserController::class, 'store'])->name('user.store');


        // Studio listing Route
        Route::get('/studio',[StudioController::class,'index'])->name('studio');
        Route::get('/studio/list', [StudioController::class, 'studio'])->name('studio.list');
        Route::get('/studio/{id}', [StudioController::class, 'getStudio'])->name('studio.get');
        Route::get('/studio/status/change', [StudioController::class, 'changeStatus'])->name('studio.status.change');
        

        //booking route
        Route::get('/booking', [BookingController::class, 'index'])->name('booking');
        Route::get('/booking/get', [BookingController::class, 'getBooking'])->name('booking.get');
        Route::get('/booking/list', [BookingController::class, 'bookings'])->name('booking.list');
        
        //coupon route 
        Route::get('/coupon', [CouponController::class, 'index'])->name('coupon');
        Route::get('/coupon/list', [CouponController::class, 'coupons'])->name('coupon.list');
        Route::get('/coupon/get', [CouponController::class, 'getCoupon'])->name('coupon.get');
        Route::post('/coupon/store', [CouponController::class, 'store'])->name('coupon.store');
        Route::post('/coupon/delete', [CouponController::class, 'delete'])->name('coupon.delete');
        
        // app setting
        Route::get('setting', [UserController::class, 'app_setting'])->name('setting');
        Route::post('setting/update', [UserController::class, 'setting_update'])->name('setting.update');

        // app version
        Route::get('version',[UserController::class,'app_version'])->name('version');
        Route::post('version/update', [UserController::class, 'version_update'])->name('version.update');

         //notification
         Route::get('notification', [NotificationController::class, 'app_notification'])->name('notification');
         Route::post('notification/send', [ NotificationController::class ,'send_notification'])->name('notification.send');

         //pages
         Route::get('/pages', [PageController::class, 'index'])->name('pages');
         Route::get('/page/list', [PageController::class, 'page'])->name('page.list');
         Route::get('/pages/edit/{page_id}', [PageController::class, 'edit'])->name('page.edit');
         Route::post('/pages/{page_id}', [PageController::class, 'save'])->name('page.save');

         //transactions
         Route::get('/transactions',[TransactionController::class, 'index'])->name('transactions');
         Route::get('/transactions/list', [TransactionController::class, 'transaction'])->name('transaction.list');
         Route::get('/transactions/{id}', [TransactionController::class, 'getTransaction'])->name('transaction.get');

         Route::post('/transfer/studio', [TransactionController::class, 'transferStudio'])->name('transaction.studio');
    });
});

Route::get('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show');






