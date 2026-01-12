<?php

namespace Webkul\SAAS\Traits;

use Webkul\SAAS\Database\Eloquent\Scopes\CompanyScope;
use Webkul\SAAS\Models\Company;

trait HasCompany
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootHasCompany()
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            if (!$model->company_id && session()->has('company_id')) {
                $model->company_id = session()->get('company_id');
            }
        });
    }

    /**
     * Get the company that owns the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
