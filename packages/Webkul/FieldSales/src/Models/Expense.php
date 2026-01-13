<?php

namespace Webkul\FieldSales\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\User\Models\User;
use Webkul\SAAS\Traits\HasCompany;
use Webkul\FieldSales\Contracts\Expense as ExpenseContract;

class Expense extends Model implements ExpenseContract
{
    use HasCompany;

    protected $table = 'field_expenses';

    protected $fillable = [
        'company_id',
        'user_id',
        'type',
        'amount',
        'description',
        'attachment_path',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
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
