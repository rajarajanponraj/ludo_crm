<?php

namespace Webkul\FieldSales\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\User\Models\User;
use Webkul\FieldSales\Contracts\UserLocation as UserLocationContract;

class UserLocation extends Model implements UserLocationContract
{
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'battery_level',
        'address',
        'tracked_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
