<?php

namespace App\Services\Addon;

use App\Models\Addon;
use App\Services\BaseService;



/**
 * BrandService
 */
class AddonService extends BaseService
{
    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(Addon $model)
    {
        parent::__construct($model);
    }
    public function createOrUpdate(array $data, $id = null)
    {
        $this->model->updateOrCreate(['name'=>$data['name']],$data);
    }
    public function all()
    {
      return $this->model->orderBy('name','asc')->paginate(10);
    }

}
