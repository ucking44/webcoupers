<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FoodMenuController;
use App\Http\Controllers\DrinkMenuController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::get('/users', [AuthController::class, 'getUsers'])->middleware('auth:api')->name('users');
    Route::get('/users/nonstaff', [AuthController::class, 'getNonStaff'])->middleware('auth:api')->name('users/nonstaff');
    Route::get('/users/{id}', [AuthController::class, 'getSingleUser'])->middleware('auth:api')->name('users');
    Route::get('/profile/{id}', [AuthController::class, 'profile'])->middleware('auth:api'); //->name('profile');


    Route::get('/uche', [AuthController::class, 'getUche'])->middleware('auth:api')->name('uche');

            //////////////////////////////// MENU ROUTE  //////////////////////////////
    Route::post('/menu', [MenuController::class, 'saveMenu'])->middleware('auth:api')->name('menu');
    Route::put('/menu/{id}', [MenuController::class, 'updateMenu'])->middleware('auth:api'); //->name('menu');
    Route::delete('/menu/{id}', [MenuController::class, 'deleteMenu'])->middleware('auth:api');

    ///////////////////////////////     // FOOD MENU ROUTE ////////////////////////////////////
    Route::post('/menu/food', [FoodMenuController::class, 'saveFoodMenu'])->middleware('auth:api'); //->name('menu/food');
    Route::put('/menu/food/{id}', [FoodMenuController::class, 'updateFoodMenu'])->middleware('auth:api'); //->name('menu/food');
    Route::delete('/menu/food/{id}', [FoodMenuController::class, 'deleteFoodMenu'])->middleware('auth:api'); //->name('menu/food');
    //Route::get('/menu/food', [FoodMenuController::class, 'getAllFoodMenus'])->middleware('auth:api')->name('menu/food');

    ///////////////////////////////     // DRINK MENU ROUTE ////////////////////////////////////
    Route::post('/menu/drink', [DrinkMenuController::class, 'saveDrinkMenu'])->middleware('auth:api'); //->name('menu/drink');
    Route::put('/menu/drink/{id}', [DrinkMenuController::class, 'updateDrinkMenu'])->middleware('auth:api'); //->name('menu/drink');
    Route::delete('/menu/drink/{id}', [DrinkMenuController::class, 'deleteDrinkMenu'])->middleware('auth:api'); //->name('menu/drink');
    //Route::get('/menu/drink', [DrinkMenuController::class, 'getAllDrinkMenus'])->middleware('auth:api')->name('menu/drink');

    Route::post('/order', [OrderController::class, 'sendOrder'])->middleware('auth:api')->name('order');
    Route::get('/order', [OrderController::class, 'placedOrder'])->middleware('auth:api')->name('order');

});

///////////////////////////////     // FOOD MENU ROUTE ////////////////////////////////////
Route::get('/menu/food', [FoodMenuController::class, 'getFoodMenus']);
Route::get('/menu/food/discount', [FoodMenuController::class, 'discount']);
Route::get('/menu/food/{id}', [FoodMenuController::class, 'getSingleFoodMenu']);

///////////////////////////////     // DRINK MENU ROUTE ////////////////////////////////////
Route::get('/menu/drink', [DrinkMenuController::class, 'getDrinkMenus']);
Route::get('/menu/drink/discount', [DrinkMenuController::class, 'discount']);
Route::get('/menu/drink/{id}', [DrinkMenuController::class, 'getSingleDrinkMenu']);

////////////////////////////////////// MENU ROUTE  ////////////////////////////////
Route::get('/menu', [MenuController::class, 'getAllMenus']);
Route::get('/menu/{id}', [MenuController::class, 'getSingleMenu']);


//// http://127.0.0.1:4444/api/auth/menu/drink/2 update
//// http://127.0.0.1:4444/api/menu/drink/2 get single



