<?php

namespace Webkul\FieldSales\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Product\Models\Product;
use Webkul\FieldSales\Contracts\OrderItem as OrderItemContract;

class OrderItem extends Model implements OrderItemContract
{
    protected $table = 'field_order_items';

    protected $fillable = [
        'field_order_id',
        'product_id',
        'qty',
        'price',
        'total',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'field_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
