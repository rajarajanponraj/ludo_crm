<?php

use Illuminate\Support\Facades\Route;
use Webkul\SAAS\Http\Controllers\CompanyController;

Route::group(['middleware' => ['web'], 'prefix' => 'register'], function () {
    Route::get('', [\Webkul\SAAS\Http\Controllers\RegistrationController::class, 'index'])->name('saas.register.index');
    Route::post('', [\Webkul\SAAS\Http\Controllers\RegistrationController::class, 'store'])->name('saas.register.store');
    Route::get('success', [\Webkul\SAAS\Http\Controllers\RegistrationController::class, 'success'])->name('saas.register.success');
});

Route::group(['middleware' => ['web', 'admin', 'auth:user'], 'prefix' => 'admin/settings'], function () {

    Route::group(['prefix' => 'companies'], function () {
        Route::get('', [CompanyController::class, 'index'])->name('saas.companies.index');
        Route::get('create', [CompanyController::class, 'create'])->name('saas.companies.create');
        Route::post('create', [CompanyController::class, 'store'])->name('saas.companies.store');
        Route::get('edit/{id}', [CompanyController::class, 'edit'])->name('saas.companies.edit');
        Route::put('edit/{id}', [CompanyController::class, 'update'])->name('saas.companies.update');
        Route::delete('delete/{id}', [CompanyController::class, 'destroy'])->name('saas.companies.delete');
    });

});
