<?php

namespace Webkul\SAAS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\SAAS\Contracts\Company as CompanyContract;

class Company extends Model implements CompanyContract
{
    use HasFactory;

    protected $table = 'companies';

    protected $fillable = [
        'name',
        'domain',
        'status',
    ];
}
