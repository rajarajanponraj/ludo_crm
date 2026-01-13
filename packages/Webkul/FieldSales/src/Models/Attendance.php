<?php

namespace Webkul\FieldSales\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\User\Models\User;
use Webkul\SAAS\Traits\HasCompany;
use Webkul\FieldSales\Contracts\Attendance as AttendanceContract;

class Attendance extends Model implements AttendanceContract
{
    use HasCompany;

    protected $table = 'attendances';

    protected $fillable = [
        'company_id',
        'user_id',
        'date',
        'check_in',
        'check_out',
        'check_in_lat',
        'check_in_lng',
        'check_out_lat',
        'check_out_lng',
        'ip_address',
        'distance_travelled',
        'late_mark',
        'early_leave',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
