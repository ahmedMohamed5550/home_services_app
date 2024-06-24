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
    Route::Post('/editUserProfile/{id}','editUserProfile')->middleware(['auth:api','isUser']);
    Route::get('/allUser','allUser')->middleware(['auth:api','isUser']);
    Route::get("user/notifications/{id}",'notifications')->middleware(['auth:api','isUser']);
    Route::get('user',"getUserDetails")->middleware(['auth:api','isUser']);
    Route::get('/logout',"logout")->middleware('auth:api');
    Route::post('/changePassword','changePassword')->middleware('auth:api');
});

// location routes

Route::group(['prefix'=>'location'],function($router){
    Route::controller(LocationController::class)->middleware(['auth:api','isUser'])->group(function(){
        Route::delete('destroy/{id}', 'destroy');
        Route::post('store', 'store');
        Route::post('update/{id}', 'update');
        Route::get('showUsersLocation/{id}', 'showUsersLocation');
    });
});

// services routes
Route::prefix('services')->group(function(){
    // show all services
    Route::get('/',[servicesController::class,'index'])->middleware(['auth:api','isUser']);
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

    Route::post('/employeeCompleteData','employeeCompleteData')->middleware('isEmployee');
    Route::Post("/updateWorksImage/{id}",'updateWorksImage')->middleware('isEmployee');
    Route::get('/showAllEmployeesByServiceId/{service_id}','showAllEmployeesByServiceId')->middleware('isUser');
    Route::get("/employeeProfile/{id}",'employeeProfile')->middleware('isEmployee');
    Route::get("/getTotalOrders/{id}/orders/total",'getTotalOrders')->middleware('isEmployee');
    Route::Post("/editEmployeeProfile/{id}",'editEmployeeProfile')->middleware('isEmployee');
    Route::get("/showEmployeeLastWorks/{id}",'showEmployeeLastWorks')->middleware('isEmployee');
    Route::post('/changeEmployeeStatus/{id}', 'changeEmployeeStatus')->middleware('isEmployee');
    Route::get("/notifications/{id}",'notifications')->middleware('isEmployee');

});

Route::controller(orderController::class)->middleware('auth:api')->group(function(){
    Route::post('makeOrder','makeOrder')->middleware('isUser');
    Route::get('getUserOrders/{id}','getUserOrders')->middleware('isUser');
    Route::get('getEmployeeOrders/{id}','getEmployeeOrders')->middleware('isEmployee');
    Route::post('changeOrderStatus/{id}', 'changeOrderStatus')->middleware('isEmployee');
    Route::get('userCancelYourOrder/{id}', 'userCancelYourOrder')->middleware('isUser');
    Route::get('orders','index');
    Route::delete('deleteOrder/{id}','deleteOrder');
});


Route::controller(FeedbackController::class)->middleware('auth:api')->group(function () {
    Route::post('makeFeedback', 'makeFeedback')->middleware('isUser');
    Route::get('getEmployeeFeedback/{id}', 'getEmployeeFeedback');
    Route::get('getAverageRatingPerEmployee', 'getAverageRatingPerEmployee');
    Route::delete('deleteFeedback/{id}', 'deleteFeedback')->middleware('isUser');
    Route::post('editFeedback/{id}', 'editFeedback')->middleware('isUser');
});

Route::controller(voucherController::class)->middleware('auth:api')->group(function () {
    Route::post('addVoucher', 'addVoucher');
    Route::get('vouchers','index')->middleware('isUser');
});


Route::prefix('sponsor')->middleware('auth:api')->group(function () {
    Route::get('/', [SponsorController::class, 'index'])->middleware('isUser');
    Route::post('/create', [SponsorController::class, 'store']);
    Route::get('/{id}', [SponsorController::class, 'show'])->middleware('isUser');
    Route::delete('/{id}', [SponsorController::class, 'destroy']);
});