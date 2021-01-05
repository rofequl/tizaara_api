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

Route::group(['middleware' => 'api', 'prefix' => 'admin', 'namespace' => 'Admin'], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::get('profile', 'AuthController@profile');
    Route::get('user-list', 'AuthController@userList');
    Route::get('supplier-list', 'AuthController@supplierList');
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
    Route::get('supplier-search', 'AuthController@supplierSearch');
});

Route::group(['middleware' => 'api', 'namespace' => 'User'], function ($router) {
    Route::resource('personal', 'ProfileController');
    Route::resource('factory', 'FactoryController');
    Route::post('factory-details', 'FactoryController@factoryDetails');
    Route::get('supplier-quotation', 'MarketingController@quotation');
    Route::post('user-product-request', 'ProductController@productRequest');
    Route::get('user-product-ecommerce-list', 'ProductController@productEcommerceList');
    Route::get('user-product-flash-list', 'ProductController@productFlashList');
});

Route::group(['middleware' => 'api'], function ($router) {
    Route::resource('brand', 'BrandController');
    Route::resource('category', 'CategoryController');
    Route::resource('subcategory', 'SubCategoryController');
    Route::resource('subsubcategory', 'SubSubCategoryController');
    Route::resource('property', 'PropertyController');
    Route::resource('product', 'ProductController');
    Route::get('product-search', 'ProductController@search');
    Route::get('product-name', 'ProductController@searchName');
    Route::resource('product-group', 'ProductGroupController');
    Route::get('get-product-group', 'ProductController@getProductGroup');
    Route::get('subcategory-slug/{data}', 'SubCategoryController@slug');
    Route::get('subsubcategory-slug/{data}', 'SubSubCategoryController@slug');
    Route::get('product-listing', 'ProductController@productListing');

    Route::resource('country', 'CountryController');
    Route::resource('division', 'DivisionController');
    Route::resource('city', 'CityController');
    Route::resource('area', 'AreaController');

    Route::post('brand-listing', 'BrandController@brandListing');
    Route::post('category-listing', 'CategoryController@categoryListing');
    Route::post('subcategory-listing', 'SubCategoryController@subcategoryListing');
    Route::post('sub-subcategory-listing', 'SubSubCategoryController@subsubcategoryListing');
    Route::get('home-banner', 'HomeSliderController@homeBannerList');
    Route::post('home-banner', 'HomeSliderController@homeBanner');
    Route::get('home-category-listing', 'HomeSliderController@homeCategoryListing');
    Route::post('home-category-listing', 'CategoryController@homeCategoryListing');

    Route::get('term_condition', 'PageManageController@termCondition');
    Route::post('term_condition', 'PageManageController@termConditionUpdate');
    Route::get('privacy_policy', 'PageManageController@privacyPolicy');
    Route::post('privacy_policy', 'PageManageController@privacyPolicyUpdate');
    Route::get('about_us', 'PageManageController@aboutUs');
    Route::post('about_us', 'PageManageController@aboutUsUpdate');
    Route::get('join_sales', 'PageManageController@joinSales');
    Route::post('join_sales', 'PageManageController@joinSalesUpdate');

    Route::get('help-category', 'HelpController@helpCategoryIndex');
    Route::post('help-category', 'HelpController@helpCategoryStore');
    Route::put('help-category/{data}', 'HelpController@helpCategoryUpdate');
    Route::delete('help-category/{data}', 'HelpController@helpCategoryDestroy');
    Route::get('help-subcategory', 'HelpController@helpSubcategoryIndex');
    Route::post('help-subcategory', 'HelpController@helpSubcategoryStore');
    Route::put('help-subcategory/{data}', 'HelpController@helpSubcategoryUpdate');
    Route::delete('help-subcategory/{data}', 'HelpController@helpSubcategoryDestroy');

    Route::get('help-question', 'HelpController@helpQuestionIndex');
    Route::post('help-question', 'HelpController@helpQuestionStore');
    Route::post('help-question/{data}/status', 'HelpController@helpQuestionStatus');
    Route::put('help-question/{data}', 'HelpController@helpQuestionUpdate');
    Route::delete('help-question/{data}', 'HelpController@helpQuestionDestroy');

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

    Route::resource('quotation', 'QuotationController');
    Route::resource('flash-deals', 'FlashDealController');
    Route::get('flash-deals-list', 'FlashDealController@flashDealList');
    Route::post('flash-deals-status', 'FlashDealController@statusUpdate');
    Route::get('request-flash-deals', 'FlashDealController@requestFlashDealList');
    Route::post('product_flash_request_input', 'FlashDealController@requestFlashDealStore');

    Route::get('newsletter', 'NewsletterController@newsletterIndex');
    Route::post('newsletter-post', 'NewsletterController@newsletterStore');
    Route::post('subscribe', 'NewsletterController@subscribeStore');

    Route::resource('testimonial', 'TestimonialController');
    Route::post('testimonial-status', 'TestimonialController@statusUpdate');
});
