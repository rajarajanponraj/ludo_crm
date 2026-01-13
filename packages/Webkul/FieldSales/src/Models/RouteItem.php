<?php

namespace Webkul\FieldSales\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Contact\Models\Person;
use Webkul\FieldSales\Contracts\RouteItem as RouteItemContract;

class RouteItem extends Model implements RouteItemContract
{
    protected $table = 'field_route_items';

    protected $fillable = [
        'field_route_id',
        'person_id',
        'target_time',
        'status',
    ];

    public function route()
    {
        return $this->belongsTo(Route::class, 'field_route_id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
