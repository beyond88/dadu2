<?php
namespace App\Jobs;

use App\Traits\Woocommerce;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class SyncProductToWooCommerceJob implements ShouldQueue
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
    public $productId;
    public $storeId;
    public $is_update;
    public function __construct($productId, $storeId, $is_update = false)
    {
        $this->productId = $productId;
        $this->storeId   = $storeId;
        $this->is_update = $is_update;
    }

    public function handle()
    {
        // Create Product in WooCommerce
        logger('calling woo commerce job');
        $this->storeProductToWoocommerce($this->productId,$this->storeId,$this->is_update);

    }
}
