<?php

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

Route::get('/', function () {
    return view('welcome');
});


//custom chat bot 
Route::get("/callback", "fbbotcontroller@callback")->middleware("fbtoken");
Route::post("/callback", "fbbotcontroller@callback");

Route::get("/signals", "fbbotcontroller@sendSellBuySignals");

Route::get("/test", "test@test");
Route::get("/kd", "test@kd");

Auth::routes();

Route::get('/payment', 'PaymentController@index')->name('payment');
Route::post('/payStripe', 'PaymentController@stripe')->name('payStripe');
Route::post('/paywithpaypal', 'PaymentController@payWithpaypal')->name('paywithpaypal');
Route::get('/status', 'PaymentController@getPaymentStatus')->name('status');
