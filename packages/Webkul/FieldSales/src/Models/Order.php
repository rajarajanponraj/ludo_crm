<?php

namespace Webkul\FieldSales\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\User\Models\User;
use Webkul\Contact\Models\Person;
use Webkul\SAAS\Traits\HasCompany;
use Webkul\FieldSales\Contracts\Order as OrderContract;

class Order extends Model implements OrderContract
{
    use HasCompany, \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'field_orders';

    protected $fillable = [
        'company_id',
        'user_id',
        'person_id',
        'type',
        'grand_total',
        'status',
        'dispatcher_id',
        'notes',
        'delivery_date',
    ];

    protected $casts = [
        'delivery_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dispatcher()
    {
        return $this->belongsTo(User::class, 'dispatcher_id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'field_order_id');
    }
}
