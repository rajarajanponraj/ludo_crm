<?php

namespace Webkul\FieldSales\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\SAAS\Traits\HasCompany;
use Webkul\FieldSales\Contracts\Announcement as AnnouncementContract;

class Announcement extends Model implements AnnouncementContract
{
    use HasCompany;

    protected $table = 'field_announcements';

    protected $fillable = [
        'company_id',
        'title',
        'content',
        'image',
        'is_active',
    ];
}
