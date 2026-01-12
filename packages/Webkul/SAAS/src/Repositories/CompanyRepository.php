<?php

namespace Webkul\SAAS\Repositories;

use Webkul\Core\Eloquent\Repository;

class CompanyRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\SAAS\Contracts\Company';
    }
}
