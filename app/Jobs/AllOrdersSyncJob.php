<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Traits\Woocommerce;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\EcomServices\EcomOrderService;

class AllOrdersSyncJob implements ShouldQueue
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
    protected $ecomOrderService;
    public function __construct(   )
    {
        $this->ecomOrderService = resolve(EcomOrderService::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try {
            $msg = "Orders ALL  sync completed";
            DB::beginTransaction();
            $orders = $this->fetchOrders();
            if(count($orders) > 0){
                foreach(array_chunk($orders,100) as $chunk){
                    $this->ecomOrderService->orderProcess($chunk);
                }
            }
            // $this->ecomOrderService->orderProcess($orders);
            if ($orders) {
                $orders_number = count($orders);
                $msg = "$orders_number orders has been fetched successfully";
            }


            echo "{$msg} \n";
            DB::commit();
        } catch (\Exception $th) {
            DB::rollBack();
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
