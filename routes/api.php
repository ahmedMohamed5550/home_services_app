<?php

use App\Http\Controllers\Auth\userAuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\orderController;
use App\Http\Controllers\servicesController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\voucherController;
use App\Models\Employee;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::controller(userAuthController::class)->group(function(){
    Route::Post('/register','register');
    Route::post('/login','login');
    Route::Post('/editUserProfile/{id}','editUserProfile')->middleware('auth:api');
    Route::get('/allUser','allUser')->middleware('auth:api');
    Route::get("user/notifications/{id}",'notifications')->middleware('auth:api');
    Route::get('user',"getUserDetails")->middleware('auth:api');
    Route::get('/logout',"logout")->middleware('auth:api');
    Route::post('/changePassword','changePassword')->middleware('auth:api');
});

// location routes

Route::group(['prefix'=>'location'],function($router){
    Route::controller(LocationController::class)->middleware('auth:api')->group(function(){
        Route::delete('destroy/{id}', 'destroy');
        Route::post('store', 'store');
        Route::post('update/{id}', 'update');
        Route::get('showUsersLocation', 'showUsersLocation');
    });
});

// services routes
Route::prefix('services')->middleware('auth:api')->group(function(){
    // show all services
    Route::get('/',[servicesController::class,'index']);
    // store new service
    Route::post('/',[servicesController::class,'store']);
    // show by id to service
    Route::get('/{id}',[servicesController::class,'show']);
    // update service
    Route::post('/{id}',[servicesController::class,'update']);
    // delete service
    Route::delete('/{id}',[servicesController::class,'destroy']);
});

Route::controller(EmployeeController::class)->prefix('employee')->middleware('auth:api')->group(function(){

    Route::post('/employeeCompleteData','employeeCompleteData');
    Route::post('/{service_id}','showAllEmployee');
    Route::Post("/updateWorksImage/{id}",'updateWorksImage');
    Route::get('/showAllEmployeesByServiceId/{service_id}','showAllEmployeesByServiceId');
    Route::get("/employeeProfile/{id}",'employeeProfile');
    Route::get("/getTotalOrders/{id}/orders/total",'getTotalOrders');
    Route::Post("/editEmployeeProfile/{id}",'editEmployeeProfile');
    Route::get("/showEmployeeLastWorks/{id}",'showEmployeeLastWorks');
    Route::post('/changeEmployeeStatus/{id}', 'changeEmployeeStatus');
    Route::get("/notifications/{id}",'notifications')->middleware('auth:api');

});

Route::controller(orderController::class)->middleware('auth:api')->group(function(){
    Route::post('makeOrder','makeOrder');
    Route::get('getUserOrders/{id}','getUserOrders');
    Route::get('getEmployeeOrders/{id}','getEmployeeOrders');
    Route::post('changeOrderStatus/{id}', 'changeOrderStatus');
    Route::get('userCancelYourOrder/{id}', 'userCancelYourOrder');
    Route::get('orders','index');
    Route::delete('deleteOrder/{id}','deleteOrder');
});


Route::controller(FeedbackController::class)->middleware('auth:api')->group(function () {
    Route::post('makeFeedback', 'makeFeedback');
    Route::get('getEmployeeFeedback/{id}', 'getEmployeeFeedback');
    Route::get('getAverageRatingPerEmployee', 'getAverageRatingPerEmployee');
    Route::delete('deleteFeedback/{id}', 'deleteFeedback');
    Route::post('editFeedback/{id}', 'editFeedback');
});

Route::controller(voucherController::class)->middleware('auth:api')->group(function () {
    Route::post('addVoucher', 'addVoucher');
    Route::get('vouchers','index');
});


Route::prefix('sponsor')->middleware('auth:api')->group(function () {
    Route::get('/', [SponsorController::class, 'index']);
    Route::post('/', [SponsorController::class, 'store']);
    Route::get('/{id}', [SponsorController::class, 'show']);
    Route::delete('/{id}', [SponsorController::class, 'destroy']);
});