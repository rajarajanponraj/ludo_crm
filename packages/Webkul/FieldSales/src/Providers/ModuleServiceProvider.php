<?php

namespace Webkul\FieldSales\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\FieldSales\Models\UserLocation::class,
        \Webkul\FieldSales\Models\Attendance::class,
        \Webkul\FieldSales\Models\Visit::class,
        \Webkul\FieldSales\Models\Route::class,
        \Webkul\FieldSales\Models\RouteItem::class,
        \Webkul\FieldSales\Models\Order::class,
        \Webkul\FieldSales\Models\OrderItem::class,
        \Webkul\FieldSales\Models\Collection::class,
        \Webkul\FieldSales\Models\Expense::class,
        \Webkul\FieldSales\Models\Target::class,
        \Webkul\FieldSales\Models\Announcement::class,
        \Webkul\FieldSales\Models\Message::class,
        \Webkul\FieldSales\Models\Leave::class,
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        \Illuminate\Support\Facades\Log::info('FieldSales Module Loadead');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'field_sales');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/menu.php',
            'menu.admin'
        );

        $this->app->register(EventServiceProvider::class);
    }

}
