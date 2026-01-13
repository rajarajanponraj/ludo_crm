<?php

namespace Webkul\FieldSales\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\User\Models\User;
use Webkul\SAAS\Traits\HasCompany;
use Webkul\FieldSales\Contracts\Message as MessageContract;

class Message extends Model implements MessageContract
{
    use HasCompany;

    protected $table = 'field_messages';

    protected $fillable = [
        'company_id',
        'sender_id',
        'receiver_id',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
