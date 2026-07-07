<?php

namespace App\Jobs\Products;

use App\Traits\Woocommerce;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Services\EcomServices\EcomCustomerService;

class SyncProductCategories implements ShouldQueue
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
    protected $id;
    protected $credential;
    protected $categoryId;
    protected $progress_id;
     // optional local category_id
    // Constructor to accept $id
    public function __construct($id,  $credential = null, $categoryId = null, $progress_id = null)
    {
        $this->id         = $id;
        $this->credential = $credential;
        $this->categoryId = $categoryId;
        $this->progress_id = $progress_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try {
            if ($this->categoryId) {
                logger('Single Product Category sync started');
                $this->syncCategoryToWooCommerce($this->id,$this->categoryId);
                $msg = "Single Product Category [ID: {$this->categoryId}] sync completed";
            } elseif($this->progress_id) {
                $this->fetchProductCategories($this->id, $this->credential, $this->progress_id);
                $msg = "All Product Categories sync completed";
            } else {
                $this->fetchProductCategories($this->id, $this->credential);
                $msg = "All Product Categories sync completed";
            }

            echo "{$msg}\n";
        } catch (\Exception $th) {
            // DB::rollBack();
            throw new \Exception($th);
        }
    }

         /**
     * Handle a job failure.
     */
    public function failed($exception): void
    {
        logger($exception);
    }
}
