<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'api', 'prefix' => 'admin', 'namespace' => 'Admin'], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::get('profile', 'AuthController@profile');
    Route::get('user-list', 'AuthController@userList');
    Route::post('user-verify/{data}', 'AuthController@userVerify');
});

Route::group(['middleware' => 'api', 'prefix' => 'user', 'namespace' => 'User'], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('login-google', 'AuthController@loginByGoogle');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('verify', 'AuthController@verify');
    Route::get('profile', 'AuthController@profile');
    Route::post('verify-send', 'AuthController@verifyTokenSend');
    Route::post('verify-request/{data}', 'AuthController@verifyRequest');
    Route::get('user-search', 'AuthController@search');
    Route::resource('product', 'ProductController');
});

Route::group(['middleware' => 'api'], function ($router) {
    Route::resource('brand', 'BrandController');
    Route::resource('category', 'CategoryController');
    Route::resource('subcategory', 'SubCategoryController');
    Route::resource('subsubcategory', 'SubSubCategoryController');
    Route::resource('property', 'PropertyController');
    Route::resource('product', 'ProductController');
    Route::get('product-search', 'ProductController@search');
    Route::resource('product-group', 'ProductGroupController');
    Route::get('get-product-group', 'ProductController@getProductGroup');
    Route::get('subcategory-slug/{data}', 'SubCategoryController@slug');
    Route::get('subsubcategory-slug/{data}', 'SubSubCategoryController@slug');
    Route::get('product-category', 'ProductController@productCategory');

    Route::resource('country', 'CountryController');
    Route::resource('division', 'DivisionController');
    Route::resource('city', 'CityController');
    Route::resource('area', 'AreaController');

    Route::resource('general-settings', 'GeneralController');
    Route::get('general-settings-logo', 'GeneralController@logo');
    Route::put('general-settings-logo/{data}', 'GeneralController@logoUpload');

    Route::resource('color', 'ColourController');
    Route::resource('attribute', 'AttributeController');
    Route::resource('unit', 'UnitController');
    Route::resource('currency', 'CurrencyController');
    Route::resource('business_type', 'BusinessTypeController');
    Route::get('currency/{data}/{data2}', 'CurrencyController@updateStatus');
    Route::get('business_type/{data}/{data2}', 'BusinessTypeController@updateStatus');
});
