<?php

use Illuminate\Http\Request;
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
App::setLocale('vi');
Route::group(['middleware' => 'api', 'namespace' => 'Site'], function ($router) {
	Route::group(['prefix' => 'auth'], function () {
        //CREDENTIAL
		Route::post('login', 'AuthController@login');
		Route::post('logout', 'AuthController@logout');
		Route::post('refresh', 'AuthController@refresh');
		Route::post('/df', 'AuthController@me');
		Route::group(['middleware' => ['roles'], 'roles' => ['customer', 'employee', 'partner']], function () {
		});
	});
	Route::group(['prefix' => 'Dofuu'], function() {
		//Check Out and Order By Customer
		Route::post('CheckCouponCode', 'CartController@checkCoupon');
		Route::post('CheckOut', 'CartController@checkOut');
		//GET PRODUCT
		Route::post('/Checkout/GetProductByStore', 'CartController@getProductByStore');
		//History Booking 
		Route::post('OrderByFilter', 'CartController@orderByFilter');
		Route::post('Order/GetOrderDetail', 'CartController@getOrderDetail');
		Route::post('Order/CancelByCustomer', 'CartController@cancelOrder');
		
	});
});

Route::group(['namespace' => 'Site'], function() {
	Route::post('/register', 'AuthController@register');
	Route::post('/user/active', 'AuthController@active');
    //FETCH CITY
	Route::get('/FetchCities', 'CityController@fetchCity');
	//GET CITY CURRENT
	Route::post('/GetCityCurrent/{city}', 'CityController@getCityCurrent');
	//GET INFORMATION CITY
	Route::get('/GetCityInformation/{city}', 'CityController@getInformation');
    //GET INFORMATION CITY HAS DEAL
	Route::get('/GetCityInformationHasDeal/{city}', 'CityController@getInformationHasDeal');
    // FETCH TYPE
	Route::get('/FetchTypes', 'TypeController@index');
	// FETCH STORE BY TYPE
	Route::post('/GetStoreByType/{city}', 'StoreController@storeByType');
    //FETCH STORE WITH DEAL BY CITY ID
	Route::get('/LoadStoreHasDeal', 'StoreController@getAllStoreWithDeal');
	//FETCH STORE 
	Route::get('/LoadStore', 'StoreController@getAllStore');
	//SHOW STORE
	Route::get('/GetStore', 'StoreController@getStore');
	
	//ASYNC AUTOCOMPLETE SEARCH
	Route::get('/Search/Bar/Query', 'StoreController@searchQuery');
	//SEARCH STORE
	Route::get('/Search/All', 'StoreController@searchStore');
	//SEARCH STORE BY PLACE
	Route::get('/Search/Places', 'StoreController@searchStoreByPlace');
	//SEARCH STORE BY TYPE
	Route::get('/Search/Types', 'StoreController@searchStoreByType');
	//SEARCH STORE BY PRODUCT
	Route::get('/Search/Products', 'StoreController@searchStoreByProduct');
});
