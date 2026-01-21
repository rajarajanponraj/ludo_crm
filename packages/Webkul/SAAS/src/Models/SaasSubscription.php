<?php

namespace Webkul\SAAS\Models;

use Illuminate\Database\Eloquent\Model;

use Webkul\SAAS\Contracts\SaasSubscription as SaasSubscriptionContract;

class SaasSubscription extends Model implements SaasSubscriptionContract
{
    protected $fillable = [
        'company_id',
        'package_id',
        'starts_at',
        'ends_at',
        'max_users',
        'status',
        'razorpay_subscription_id',
        'amount_paid',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];

    public function package()
    {
        return $this->belongsTo(SaasPackage::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function isValid()
    {
        return $this->status === 'active' && $this->ends_at->isFuture();
    }
}
