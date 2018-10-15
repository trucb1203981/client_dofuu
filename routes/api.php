<?php

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
		Route::get('/login/facebook', 'AuthController@socialLogin');
		Route::get('/login/facebook/callback', 'AuthController@handleProviderCallBack');
		Route::post('logout', 'AuthController@logout');
		Route::post('refresh', 'AuthController@refresh');
		Route::post('/df', 'AuthController@me');
		Route::group(['middleware' => ['roles'], 'roles' => ['customer', 'employee', 'partner']], function () {
		});
	});
	Route::group(['prefix' => 'Dofuu'], function () {
		//Check Out and Order By Customer
		Route::post('CheckCouponCode', 'CartController@checkCoupon');
		Route::post('CheckOut', 'CartController@checkOut');
		//History Booking
		Route::post('OrderByFilter', 'CartController@orderByFilter');
		//ORDER DETAILS
		Route::post('Order/GetOrderDetail', 'CartController@getOrderDetail');
		//CANCEL ORDER
		Route::post('Order/CancelByCustomer', 'CartController@cancelOrder');
		//GET USER
		Route::get('/GetUser', 'UserController@getUser');
		//EDIT INFORMATION
		Route::post('/GetUser/EditInformation', 'UserController@editUser');
		//CHANGE PASSWORD
		Route::post('/GetUser/ChangePassword', 'UserController@changePassword');
	});
});

Route::group(['namespace' => 'Site'], function () {
	//REGISTER ACCOUNT
	Route::post('/register', 'AuthController@register');
	//ACTIVE ACCOUNT
	Route::post('/user/active', 'AuthController@active');
	//LOGIN FACEBOOK
	Route::post('/facebook/auth', 'AuthController@loginFB');
	//REGISTER FACEBOOK
	Route::post('/facebook/register', 'AuthController@registerFB');
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
	//GET PRODUCT
	Route::get('/GetStore/{sid}/Product', 'ProductController@getProductByStore');
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
	//TOGGLE LIKE STORE
	Route::post('/LikeStore/{storeId}/Toggle', 'LikeController@toggleLikeStore');
	//TOGGLE LIKE COMMENT
	Route::post('/LikeComment/{commentId}/Toggle', 'LikeController@toggleLikeComment');

	Route::group(['prefix' => 'FavoriteStore'], function () {
		//GET FAVORITE STORE
		Route::post('/FetchStores', 'FavoriteController@fetchFavoriteStore');
		//TOGGLE FAVORITE STORE
		Route::post('/{storeId}/ToggleFavorite', 'FavoriteController@toggleFavoriteStore');
		//REMOVE FAVORITE STORE
		Route::post('/{storeId}/RemoveFavorite', 'FavoriteController@removeFavoriteStore');
	});

	Route::group(['prefix' => 'CommentStore'], function () {
		//FETCH COMMENT
		Route::post('/{storeId}/FetchComments', 'CommentController@fetchComments');
		//FETCH REPLIES
		Route::post('/{storeId}/FetchReplies', 'CommentController@fetchReplies');
		//ADD REPLY
		Route::post('/{storeId}/AddReply', 'CommentController@addReply');
		//ADD COMMENT
		Route::post('/{storeId}/AddComment', 'CommentController@addComment');
		//EDIT COMMENT
		Route::post('/{storeId}/UpdateComment', 'CommentController@updateComment');
		//REMOVE COMMENT
		Route::post('/{storeId}/RemoveComment', 'CommentController@removeComment');
	});

	Route::group(['prefix' => 'RatingStore'], function () {
		// ADD RATING
		Route::post('/{storeId}/NewRating', 'RatingController@addRating');
		// FETCH RATING
		Route::post('/{storeId}/FetchRating', 'RatingController@fetchRating');
		// REMOVE RATING
		Route::post('/{storeId}/RemoveRating', 'RatingController@removeRating');
	});

	Route::group(['prefix' => 'Auth'], function () {
		// UPDATE PHONE NUMBER
		Route::post('/PhoneNumber/Update', 'UserController@updatePhone');
		Route::post('/Avatar/Update', 'UserController@updateAvatar');
	});

	Route::get('/Maps/MyLocation', 'StoreController@getStoreByDistance');

});

Route::group(['namespace' => 'Mobile', 'prefix' => 'm'], function () {
	//FETCH CITY
	Route::get('/FetchCities', 'CityController@fetchCity');
	//GET CITY CURRENT
	Route::get('/FetchCity/{city}', 'CityController@getCityCurrent');
	//GEt CITY INFORMATION HAS DEAL
	Route::get('/FetchCity/{city}/GetInformationHasDeal', 'CityController@getInformationHasDeal');
	//GET CITY INFORMATION
	Route::get('/FetchCity/{city}/GetInformation', 'CityController@getInformation');
	//FETCH STORES
	Route::get('/GetStore/{id}', 'StoreController@showStore');

	Route::group(['prefix' => 'Type'], function() {
		Route::post('/FetchAllTypes', 'TypeController@fetchType');
	});

	Route::group(['prefix' => 'City'], function() {
		Route::post('/FetchAllCities', 'CityController@fetchCity');
		Route::post('/{city}/GetCurrentCity', 'CityController@getCurrentCity');
	});

	Route::group(['prefix' => 'Store'], function() {
		//GET ALL STORE HAS DEAL
		Route::post('/FetchDealStores', 'StoreController@fetchStoreHasDeal');
		//GET ALL STORE
		Route::post('/FetchAllStores', 'StoreController@fetchAllStore');

	});
	
	Route::get('/FetchType/Stores', 'StoreController@fetchAllStoreByType');
});