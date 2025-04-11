<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\TenderController;
use App\Http\Controllers\Api\UserController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


/**
 * Authentication routes for the API
 */

Route::group(['prefix' => 'auth'], function () {
    Route::controller(AuthController::class)->group(function () {
        // nikita sms
        Route::post('test-login', 'testLogin');

        Route::post('send-otp', 'sendOtpTwillio');
        Route::post('verify-otp', 'verifyOtpTwillio');

        //twillo
        Route::post('generate-otp', 'sendOtpTwillio');
        Route::post('verify-twillio-otp', 'verifyOtpTwillio');
    });
});

Route::group(['middleware' => ['auth:api']], function () {
    Route::controller(UserController::class)->group(function () {
        Route::group(['prefix' => 'users'], function () {
            Route::get('info', 'info');
            Route::post('update', 'update');
            Route::post('verify', 'verify');
            Route::post('fcm-token-update', 'fcm_token_update');
            Route::post('image-update', 'update_image');
            Route::post('change-password', 'change_password');
            Route::delete('delete', 'delete');
        });
    });

    Route::controller(CompanyController::class)->group(function () {
        Route::group(['prefix' => 'companies'], function () {
            Route::get('/my', 'my');
            Route::post('store', 'store');
            Route::get('{company}', 'show');
            Route::post('update/{company}', 'update');
            Route::delete('delete/{company}', 'delete');
            Route::get('verify/{tin}', 'verify');
            Route::get('documents/{company}', 'documents');
            Route::post('documents-upload/{company}', 'uploadDocument');
            Route::delete('documents-delete/{company}', 'deleteDocument');
            Route::get('{company}/tenders', 'tenders');
            Route::post('store-review/{company}', 'storeReview');
            Route::get('{company}/reviews', 'reviews');
            Route::get("get-company-complain/{company}", 'getCompanyComplains');
            Route::post("company-complain/{company}", 'storeComplain');
        });
    });

    Route::controller(AccountController::class)->group(function () {
        Route::group(['prefix' => 'accounts'], function () {
            Route::get('/my', 'my');
            Route::post('store', 'store');
            Route::get('{account}', 'show');
            Route::post('update/{account}', 'update');
            Route::delete('delete/{account}', 'delete');
        });
    });
    Route::controller(TenderController::class)->group(function () {
        Route::group(['prefix' => 'tenders'], function () {
            Route::post('store', 'store');
            Route::get('{tender}', 'show');
            Route::post('update/{tender}', 'update');
            Route::delete('delete/{tender}', 'delete');
            Route::delete('{tender}/images/{mediaId}', 'removeImage');
            Route::post('{tender}/active-status', 'activeStatus');
        });
    });

    Route::controller(ProductController::class)->group(function () {

        Route::group(['prefix' => 'products'], function () {
            Route::get('/', 'index');
            Route::post('store', 'store');
            Route::post('/upload-images/{product}', 'uploadImages');
            Route::post('/{product}/variation-types',  'storeOrUpdateVariationTypes');
            Route::get('/{product}/variation-setup',  'getProductVariations');
            Route::post('/{product}/variationUpdate',  'updateProductVariations');
            Route::get('{product}', 'show');
            Route::post('update/{product}', 'update');
            Route::delete('delete/{product}', 'delete');
        });
    });

    Route::controller(ReviewController::class)->group(function () {
        Route::group(['prefix' => 'reviews'], function () {
            Route::post('store', 'store');
            Route::delete('/delete/{review}', 'destroy');
            Route::patch('/{review}/approve', 'approve');
            Route::get('/my', 'myReviews');
            Route::post('/{review}/reply', 'reply');
        });
    });
});

Route::controller(ProductController::class)->group(function () {
    Route::get('search', 'search');
    Route::get('categories', 'categories');
    Route::get("category/{category}", 'categoryDetail');
});
