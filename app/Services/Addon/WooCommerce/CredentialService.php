<?php
namespace App\Services\Addon\WooCommerce;

use App\Models\Platform;
use App\Services\BaseService;

class CredentialService extends BaseService

{
    protected $model;

    public function __construct(Platform $platform)
    {
       $this->model = $platform;
       
    }


}
