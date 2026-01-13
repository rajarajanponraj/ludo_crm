<?php

namespace Webkul\FieldSales\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\User\Models\User;
use Webkul\SAAS\Traits\HasCompany;
use Webkul\FieldSales\Contracts\Route as RouteContract;

class Route extends Model implements RouteContract
{
    use HasCompany;

    protected $table = 'field_routes';

    protected $fillable = [
        'company_id',
        'user_id',
        'date',
        'name',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(RouteItem::class, 'field_route_id');
    }
}
