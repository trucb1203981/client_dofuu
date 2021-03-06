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
		Route::post('CheckCouponCode', 'CouponController@checkCoupon');
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
	Route::get('/FetchStoreHasDeal', 'StoreController@getAllStoreWithDeal');
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
	//FETCH STORES
	Route::get('/GetStore/{id}', 'StoreController@showStore');
	//CREDENTIALS
	Route::group(['prefix' => 'Credentials'], function() {
		//LOGIN
		Route::post('/Login', 'AuthController@login');
		//LOGIN FACEBOOK
		Route::post('/Facebook/Login', 'AuthController@loginFB');
		//REGISTER
		Route::post('/Register', 'AuthController@register');
		//FACEBOOK REGISTER
		Route::post('/Facebook/Register', 'AuthController@registerFB');
	});
	//USER
	Route::group(['prefix' => 'Users'], function() {
		Route::post('/GetAuth', 'AuthController@me');
		Route::post('/ChangePassword', 'UserController@changePassword');
	});
	//TYPE
	Route::group(['prefix' => 'Type'], function() {
		//FETCH TYPE
		Route::post('/FetchAllTypes', 'TypeController@fetchType');
	});
	//CITY
	Route::group(['prefix' => 'City'], function() {
		//FETCH CITY
		Route::post('/FetchAllCities', 'CityController@fetchCity');
		//GET CITY CURRENT
		Route::post('/{cityId}/ShowCity', 'CityController@getCurrentCity');
	});
	//PRODUCT
	Route::group(['prefix' => 'Product'], function() {
		Route::post('/SearchByName', 'ProductController@search');
	});
	//CATALOGUE
	Route::group(['prefix' => 'Catalogue'], function() {
		Route::post('/FetchAllCatalogue', 'CatalogueController@fetch');
	});
	//STORE
	Route::group(['prefix' => 'Store'], function() {
		//GET ALL STORE HAS DEAL
		Route::post('/FetchDealStores', 'StoreController@fetchStoreHasDeal');
		//GET ALL STORE
		Route::post('/FetchAllStores', 'StoreController@fetchAllStore');
		//GET ALL STORE BY TYPE
		Route::post('/FetchStoresByType', 'StoreController@storeByType');
		//SHOW STORE
		Route::post('/{storeId}/ShowStore', 'StoreController@getCurrentStore');
		//SEARCH STORE
		Route::post('/Search', 'StoreController@search');
	});
	//COUPON
	Route::group(['prefix' => 'Coupon'], function() {
		//GET ALL COUPON IN STORE
		Route::post('/FetchCouponsByStore', 'CouponController@fetchCouponByStore');
		//CHECK COUPON
		Route::post('/CheckCoupon', 'CouponController@checkCoupon');
	});
	//CART
	Route::group(['prefix' => 'Cart'], function() {
		//CHECK OUT
		Route::post('/Checkout', 'CartController@checkout');
	});
	//ORDER
	Route::group(['prefix' => 'Order'], function() {
		Route::post('/FetchOrderByFilter', 'OrderController@orderByFilter');
		Route::post('/{orderId}/Show', 'OrderController@show');
		Route::post('/{orderId}/Cancel', 'OrderController@cancel');
	});
	//STATUS
	Route::group(['prefix' => 'Status'], function() {
		//GET STATUS
		Route::post('/FetchOrderStatus', 'OrderStatusController@getOrderStatus');
	});

	Route::get('/FetchType/Stores', 'StoreController@fetchAllStoreByType');
});