<?php

namespace Webkul\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Webkul\SAAS\Traits\HasCompany;
use Webkul\User\Contracts\User as UserContract;

class User extends Authenticatable implements UserContract
{
    use HasApiTokens, HasCompany, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'api_token',
        'role_id',
        'view_permission',
        'status',
        'company_id',
        'is_superuser',
        'reports_to', // Added for Hierarchy
    ];

    /**
     * Get the manager of the user.
     */
    public function manager()
    {
        return $this->belongsTo(self::class, 'reports_to');
    }

    /**
     * Get the subordinates of the user.
     */
    public function subordinates()
    {
        return $this->hasMany(self::class, 'reports_to');
    }

    /**
     * Get all recursive subordinates IDs.
     */
    public function getSubordinateIds()
    {
        $ids = $this->subordinates()->pluck('id')->toArray();
        foreach ($this->subordinates as $subordinate) {
            $ids = array_merge($ids, $subordinate->getSubordinateIds());
        }
        return array_unique($ids);
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'api_token',
        'remember_token',
    ];

    /**
     * Get image url for the product image.
     */
    public function image_url()
    {
        if (!$this->image) {
            return;
        }

        return Storage::url($this->image);
    }

    /**
     * Get image url for the product image.
     */
    public function getImageUrlAttribute()
    {
        return $this->image_url();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();

        $array['image_url'] = $this->image_url;

        return $array;
    }

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(RoleProxy::modelClass());
    }

    /**
     * The groups that belong to the user.
     */
    public function groups()
    {
        return $this->belongsToMany(GroupProxy::modelClass(), 'user_groups');
    }

    /**
     * Checks if user has permission to perform certain action.
     *
     * @param  string  $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        if ($this->is_superuser) {
            return true;
        }

        if ($this->role->permission_type === 'all') {
            return true;
        }

        if ($this->role->permission_type == 'custom' && !$this->role->permissions) {
            return false;
        }

        return in_array($permission, $this->role->permissions);
    }
}
