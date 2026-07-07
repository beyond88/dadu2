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

class SyncProducts implements ShouldQueue
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
    protected $progress_id;

    // Constructor to accept $id
    public function __construct($id,$credential, $progress_id)
    {
        $this->id = $id;
        $this->credential= $credential;
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
            $msg = "Products sync completed";
            // DB::beginTransaction();
            $this->fetchProducts($this->id,$this->credential,$this->progress_id);
            echo "{$msg} \n";
            // DB::commit();
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
