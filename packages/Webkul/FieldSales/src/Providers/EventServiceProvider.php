<?php

namespace Webkul\FieldSales\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'field_sales.order.created' => [
            'Webkul\FieldSales\Listeners\OrderNotification',
        ],
    ];
}
