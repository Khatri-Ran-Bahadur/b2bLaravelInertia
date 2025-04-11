<?php

use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TenderCategoryController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TenderController;
use App\Http\Middleware\AdminCheckMiddleware;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', AdminCheckMiddleware::class])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {

        // Company routes
        Route::controller(CompanyController::class)->group(function () {
            Route::prefix('companies')->name('companies.')->group(function () {
                Route::get('/{company}/products', 'products')->name('products');
                Route::get('/{company}/tenders', 'tenders')->name('tenders');
                Route::get('/{company}/documents', 'documents')->name('documents');
                Route::get('/{company}/reviews', 'reviews')->name('reviews');
            });
        });


        Route::resources([
            'users' => UserController::class,
            'companies' => CompanyController::class,
            'tender-categories' => TenderCategoryController::class,
            'tenders' => TenderController::class,
            'categories' => CategoryController::class,
        ]);
    });
});
