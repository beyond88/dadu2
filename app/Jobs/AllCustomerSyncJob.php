<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Services\EcomServices\EcomCustomerService;

class AllCustomerSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    public $timeout = 800;
    protected $eComCustomerService;
    public function __construct( )
    {
        $this->eComCustomerService = resolve(EcomCustomerService::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $numberOfChunk = 100;
        try {
            DB::beginTransaction();
            $apiDatas = $this->eComCustomerService->wooComfetchAllCustomers();
            foreach(array_chunk($apiDatas,$numberOfChunk) as $key =>$apiData){
                $this->eComCustomerService->updateOrCreateFetchData($apiData);
                echo "Chumk number :{$key} completed \n";
                DB::commit();
            }
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
