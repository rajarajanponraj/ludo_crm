<?php

namespace Webkul\FieldSales\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\User\Models\User;
use Webkul\SAAS\Traits\HasCompany;
use Webkul\FieldSales\Contracts\Leave as LeaveContract;

class Leave extends Model implements LeaveContract
{
    use HasCompany;

    protected $table = 'field_leaves';

    protected $fillable = [
        'company_id',
        'user_id',
        'start_date',
        'end_date',
        'type',
        'reason',
        'status',
        'approved_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
