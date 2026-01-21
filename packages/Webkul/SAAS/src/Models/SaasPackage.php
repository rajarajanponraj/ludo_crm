<?php

namespace Webkul\SAAS\Models;

use Illuminate\Database\Eloquent\Model;

use Webkul\SAAS\Contracts\SaasPackage as SaasPackageContract;

class SaasPackage extends Model implements SaasPackageContract
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'included_users',
        'extra_user_price',
        'features',
        'status',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'extra_user_price' => 'decimal:2',
        'status' => 'boolean',
    ];
}
