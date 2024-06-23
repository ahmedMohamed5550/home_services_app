<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\admin\employeeController;
use App\Http\Controllers\admin\servicesController;
use App\Http\Controllers\admin\voucherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view("welcome");
});

Route::group(['prefix' => 'admin'], function () {

    Route::controller(AdminController::class)->group(function () {
        Route::get('/register', 'showRegisterForm')->middleware("isAdmin");
        Route::post('/register', 'register')->name("register")->middleware("isAdmin");
        Route::get('/login', 'showLoginForm');
        Route::post('/login', 'login')->name("login");
        Route::get("/redirect", 'redirect');
        Route::get('/logout', 'logout')->name('logout');
    });



    Route::controller(servicesController::class)->group(function () {

        Route::middleware("isAdmin")->group(function () {
            Route::get('/addServices', [servicesController::class, 'showServicesForm']);
            Route::post('/services', 'storeServices');
            Route::get('/services', 'getAllServices');
            Route::get('/services/show/{id}', 'showServices');
            Route::get('/services/edit/{id}', 'showEditForm');
            Route::put('/services/edit/{id}', 'editServices');
            Route::delete('/services/delete/{id}', 'deleteServices');
        });
    });




    Route::controller(employeeController::class)->group(function () {
        Route::middleware("isAdmin")->group(function () {
            Route::get('/employee/all', 'getUserEmployeeData');
            Route::get('/checkByAdmin/{employeeId}/{status}', 'checkByAdmin');
        });
    });


    Route::controller(voucherController::class)->group(function () {
        Route::middleware("isAdmin")->group(function () {
            Route::get('/addVoucher', 'showVoucherForm');
            Route::post('/Voucher', 'storeVoucher');
            Route::get('/Voucher', 'getAllVoucher');
            Route::get('/voucher/show/{id}', 'showVoucher');
            Route::get('/voucher/edit/{id}', 'showEditForm');
            Route::put('/voucher/edit/{id}', 'editVoucher');
            Route::delete('/voucher/delete/{id}', 'deleteVoucher');
        });
    });

});