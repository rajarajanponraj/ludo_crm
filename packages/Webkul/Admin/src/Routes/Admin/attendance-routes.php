<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\AttendanceController;

Route::controller(AttendanceController::class)->prefix('attendance')->group(function () {
    Route::get('', 'index')->name('admin.attendance.index');
    Route::post('', 'store')->name('admin.attendance.store');
    Route::put('{id}', 'update')->name('admin.attendance.update');
});
