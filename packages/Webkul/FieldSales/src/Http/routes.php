<?php

use Illuminate\Support\Facades\Route;
use Webkul\FieldSales\Http\Controllers\Api\LocationController;
use Webkul\FieldSales\Http\Controllers\Api\AttendanceController;
use Webkul\FieldSales\Http\Controllers\Api\VisitController;
use Webkul\FieldSales\Http\Controllers\Api\RouteController;

Route::group(['prefix' => 'api/field-sales', 'middleware' => ['api', 'auth:sanctum']], function () {
    Route::post('location', [LocationController::class, 'store'])->name('field-sales.api.location.store');

    Route::post('attendance/check-in', [AttendanceController::class, 'checkIn'])->name('field-sales.api.attendance.check-in');
    Route::post('attendance/check-out', [AttendanceController::class, 'checkOut'])->name('field-sales.api.attendance.check-out');

    Route::post('visits/check-in', [VisitController::class, 'checkIn'])->name('field-sales.api.visits.check-in');
    Route::post('visits/check-out', [VisitController::class, 'checkOut'])->name('field-sales.api.visits.check-out');

    Route::get('my-route', [RouteController::class, 'index'])->name('field-sales.api.routes.index');

    Route::get('catalog', [\Webkul\FieldSales\Http\Controllers\Api\CatalogController::class, 'index'])->name('field-sales.api.catalog.index');

    Route::get('orders', [\Webkul\FieldSales\Http\Controllers\Api\OrderController::class, 'index'])->name('field-sales.api.orders.index');
    Route::post('orders', [\Webkul\FieldSales\Http\Controllers\Api\OrderController::class, 'store'])->name('field-sales.api.orders.store');

    Route::get('collections', [\Webkul\FieldSales\Http\Controllers\Api\CollectionController::class, 'index'])->name('field-sales.api.collections.index');
    Route::post('collections', [\Webkul\FieldSales\Http\Controllers\Api\CollectionController::class, 'store'])->name('field-sales.api.collections.store');

    Route::get('expenses', [\Webkul\FieldSales\Http\Controllers\Api\ExpenseController::class, 'index'])->name('field-sales.api.expenses.index');
    Route::post('expenses', [\Webkul\FieldSales\Http\Controllers\Api\ExpenseController::class, 'store'])->name('field-sales.api.expenses.store');

    Route::get('announcements', [\Webkul\FieldSales\Http\Controllers\Api\CommunicationController::class, 'announcements'])->name('field-sales.api.announcements.index');
    Route::get('messages', [\Webkul\FieldSales\Http\Controllers\Api\CommunicationController::class, 'inbox'])->name('field-sales.api.messages.index');
    Route::post('messages', [\Webkul\FieldSales\Http\Controllers\Api\CommunicationController::class, 'sendMessage'])->name('field-sales.api.messages.store');

    Route::get('sync', [\Webkul\FieldSales\Http\Controllers\Api\DataController::class, 'sync'])->name('field-sales.api.data.sync');

    Route::get('leaves', [\Webkul\FieldSales\Http\Controllers\Api\LeaveController::class, 'index'])->name('field-sales.api.leaves.index');
    Route::post('leaves', [\Webkul\FieldSales\Http\Controllers\Api\LeaveController::class, 'store'])->name('field-sales.api.leaves.store');
});

Route::group(['middleware' => ['web', 'admin'], 'prefix' => 'admin/field-sales'], function () {
    Route::get('routes', [\Webkul\FieldSales\Http\Controllers\Admin\RouteController::class, 'index'])->name('field_sales.admin.routes.index');
    Route::get('routes/create', [\Webkul\FieldSales\Http\Controllers\Admin\RouteController::class, 'create'])->name('field_sales.admin.routes.create');
    Route::post('routes/store', [\Webkul\FieldSales\Http\Controllers\Admin\RouteController::class, 'store'])->name('field_sales.admin.routes.store');

    Route::get('dispatch', [\Webkul\FieldSales\Http\Controllers\Admin\DispatchController::class, 'index'])->name('field_sales.admin.dispatch.index');
    Route::post('dispatch/{id}/assign', [\Webkul\FieldSales\Http\Controllers\Admin\DispatchController::class, 'assign'])->name('field_sales.admin.dispatch.assign');
    Route::post('dispatch/{id}/mark-dispatched', [\Webkul\FieldSales\Http\Controllers\Admin\DispatchController::class, 'dispatchOrder'])->name('field_sales.admin.dispatch.dispatch');

    Route::get('reports', [\Webkul\FieldSales\Http\Controllers\Admin\ReportController::class, 'index'])->name('field_sales.admin.reports.index');

    Route::get('expenses', [\Webkul\FieldSales\Http\Controllers\Admin\ExpenseController::class, 'index'])->name('field_sales.admin.expenses.index');
    Route::post('expenses/{id}/approve', [\Webkul\FieldSales\Http\Controllers\Admin\ExpenseController::class, 'approve'])->name('field_sales.admin.expenses.approve');
    Route::post('expenses/{id}/reject', [\Webkul\FieldSales\Http\Controllers\Admin\ExpenseController::class, 'reject'])->name('field_sales.admin.expenses.reject');

    Route::get('leaves', [\Webkul\FieldSales\Http\Controllers\Admin\LeaveController::class, 'index'])->name('field_sales.admin.leaves.index');
    Route::put('leaves/{id}', [\Webkul\FieldSales\Http\Controllers\Admin\LeaveController::class, 'update'])->name('field_sales.admin.leaves.update');

    Route::get('data-tools', [\Webkul\FieldSales\Http\Controllers\Admin\ImportExportController::class, 'index'])->name('field_sales.admin.data_tools.index');
    Route::get('data-tools/export-orders', [\Webkul\FieldSales\Http\Controllers\Admin\ImportExportController::class, 'exportOrders'])->name('field_sales.admin.export.orders');
    Route::post('data-tools/import-products', [\Webkul\FieldSales\Http\Controllers\Admin\ImportExportController::class, 'importProducts'])->name('field_sales.admin.import.products');
});
