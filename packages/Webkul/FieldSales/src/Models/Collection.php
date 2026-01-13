<?php

namespace Webkul\FieldSales\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\User\Models\User;
use Webkul\Contact\Models\Person;
use Webkul\SAAS\Traits\HasCompany;
use Webkul\FieldSales\Contracts\Collection as CollectionContract;

class Collection extends Model implements CollectionContract
{
    use HasCompany;

    protected $table = 'field_collections';

    protected $fillable = [
        'company_id',
        'user_id',
        'person_id',
        'invoice_id',
        'amount',
        'payment_mode',
        'transaction_id',
        'proof_image',
        'collected_at',
    ];

    protected $casts = [
        'collected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
