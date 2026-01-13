<?php

namespace Webkul\FieldSales\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\User\Models\User;
use Webkul\Contact\Models\Person;
use Webkul\SAAS\Traits\HasCompany;
use Webkul\FieldSales\Contracts\Visit as VisitContract;

class Visit extends Model implements VisitContract
{
    use HasCompany, \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'company_id',
        'user_id',
        'person_id',
        'check_in_at',
        'check_out_at',
        'latitude',
        'longitude',
        'feedback',
        'images',
        'status',
    ];

    protected $casts = [
        'images' => 'array',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
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
