<?php

namespace Webkul\FieldSales\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\User\Models\User;
use Webkul\SAAS\Traits\HasCompany;
use Webkul\FieldSales\Contracts\Target as TargetContract;

class Target extends Model implements TargetContract
{
    use HasCompany;

    protected $table = 'field_targets';

    protected $fillable = [
        'company_id',
        'user_id',
        'type',
        'target_value',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
