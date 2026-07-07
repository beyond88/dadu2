<?php
namespace App\Jobs;

use App\Traits\Woocommerce;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class SyncWooCommerceStockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Woocommerce;

     /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;


    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions =6;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 2000;
    public $storeId;
    public $data;
    public $productId;
    public $is_purchase;

    public function __construct($storeId,$productId = null,$data,$is_purchase=false)
    {
        $this->storeId   = $storeId;
        $this->productId = $productId;
        $this->data = $data;
        $this->is_purchase = $is_purchase;

    }

    public function handle()
    {
        if($this->is_purchase)
        {
            $this->updateWooCommerceStoresStock($this->storeId,$this->data);

        }else
        {
              $this->syncWoocommerceStock($this->storeId,$this->productId,$this->data);
            
        }
        // Create Product in WooCommerce

    }
}
