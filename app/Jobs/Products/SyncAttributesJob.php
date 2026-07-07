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

class SyncAttributesJob implements ShouldQueue
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
    protected $eComCustomerService;
    protected $id;
    protected $credential;
    protected $attributeId;
    protected $is_all;
    protected $progress_id;
    // Constructor to accept $id
    public function __construct($id,$credential = null,$attributeId = null,$is_all=false, $progress_id = null)
    {
        $this->id = $id;
        $this->credential= $credential;
        $this->attributeId = $attributeId;
        $this->is_all = $is_all;
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
            if ($this->attributeId) {
                logger('Single attribute sync started');
                $this->syncAttributeToWooCommerce($this->id, $this->attributeId);
                $msg = "Single attribute [ID: {$this->attributeId}] sync completed";
            } elseif($this->is_all) {
                logger('All attributes sync started');
                $this->fetchAndStoreAttributes($this->id, $this->credential, $this->progress_id);
                $msg = "All attributes sync completed";
            }else {
                $this->fetchAttributes($this->id, $this->credential);
                $msg = "All attributes sync completed";
            }

            echo "{$msg}\n";

        } catch (\Exception $e) {
            throw new \Exception($e);
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
