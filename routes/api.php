<?php

use App\Http\Controllers\BookATruckController;
use App\Http\Controllers\BookAMoverController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovingDetailsController;
use App\Http\Controllers\DeliveryDetailController;
use App\Http\Middleware\IsUser;
use App\Models\BookAMover;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


    Route::post('sign_in', [UserController::class, 'login']);
    Route::post('user_registration', [UserController::class, 'register']);
    Route::post('forgot_password', [UserController::class, 'forgotPassword']);
    Route::post('otp_verification', [UserController::class, 'verifyOtp']);
    Route::post('create_password', [UserController::class, 'createPassword']);
    Route::post('social_user_existance',[UserController::class,'social_user_existance']);
    Route::post('social_user_registration',[UserController::class,'social_user_registration']);
   

    Route::middleware(['auth:sanctum', 'isUser'])->group(function () {
        Route::get('delete_account',[UserController::class, 'delete_account']);
        Route::post('moving_details', [MovingDetailsController::class, 'storeMoveDetails'])->name('moving_details');
        Route::post('resend_otp', [UserController::class, 'resendOtp']);
        Route::post('edit_profile', [UserController::class, 'editProfile']);
        Route::get('user_get_moving_details',[MovingDetailsController::class ,'user_get_moving_details']);
        Route::get('user_get_delivery_details',[DeliveryDetailController::class ,'user_get_delivery_details']);
        Route::post('delivery-details', [DeliveryDetailController::class, 'storeDeliveryDetails']);
        Route::post('get_distance',[MovingDetailsController::class, 'get_distance']);
        Route::get('user_get_moving_details_by_id/{id}',[MovingDetailsController::class,'user_get_moving_details_by_id']);
        Route::get('user_get_delivery_details_by_id/{id}',[DeliveryDetailController::class,'user_get_delivery_details_by_id']);
        Route::get('shippment_history',[DeliveryDetailController::class,'shippment_history']);
        Route::get('logout',[Usercontroller::class, 'logout']);
        Route::post('delivery_cost_calculation',[DeliveryDetailController::class,'delivery_cost_calculation']);
        Route::get('get_user_data',[UserController::class,'getUserData']);
        Route::post('book_a_truck',[BookATruckController::class,'BookingATruck']);
        Route::post('book_a_mover',[BookAMoverController::class,'bookingAmover']);
        Route::get('get_book_a_movers_details',[BookAMoverController::class,'getBookAMoverDetails']);
        Route::get('get_book_a_movers_detail_by_id/{id}',[BookAMoverController::class,'getBookAMoverDetailsById']);
        Route::get('get_book_a_truck_details',[BookATruckController::class,'getBookATruckDetails']);
        Route::get('get_book_a_truck_detail_by_id/{id}',[BookATruckController::class,'getBookATruckDetailsById']);
    });
    Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
        Route::get('get_delivery_details',[DeliveryDetailController::class, 'get_delivery_details']);
        Route::get('get_moving_details',[MovingDetailsController::class, 'get_moving_details']);
        Route::get('admin_get_delivery_details',[DeliveryDetailController::class,'admin_get_delivery_details']);
        Route::get('admin_get_delivery_details_by_id/{id}',[DeliveryDetailController::class,'admin_get_delivery_details_by_id']);
        Route::get('admin_get_moving_details',[MovingDetailsController::class,'admin_get_moving_details']);
        Route::get('admin_get_moving_details_by_id/{id}',[MovingDetailsController::class,'admin_get_moving_details_by_id']);
        Route::patch('admin_update_delivery_status/{id}',[DeliveryDetailController::class,'admin_update_delivery_status']);
        Route::patch('admin_update_moving_status/{id}',[MovingDetailsController::class, 'admin_update_moving_status']);
        Route::post('change_status',[DeliveryDetailController::class,'change_status']);
    }); 


