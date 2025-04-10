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
        Route::resources([
            'users' => UserController::class,
            'companies' => CompanyController::class,
            'tender-categories' => TenderCategoryController::class,
            'tenders' => TenderController::class,
            'categories' => CategoryController::class,
        ]);
    });
});
