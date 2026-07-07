<?php

namespace App\Jobs;

use App\Traits\Woocommerce;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\DB;
use App\Models\ProgressTracking;

class FetchWooProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Woocommerce;

    public $tries = 5;
    public $maxExceptions = 6;
    public $timeout = 2000;

    protected $products;
    protected $platform_id;
    protected $credential;
    protected $page;
    protected $progress_id;

    public function __construct($products, $platform_id, $credential, $page, $progress_id)
    {
        $this->products = $products;
        $this->platform_id = $platform_id;
        $this->credential = $credential;
        $this->page = $page;
        $this->progress_id = $progress_id;
    }

    public function handle()
{
    logger('job called');

    foreach ($this->products as $api_product) {
        DB::beginTransaction();
        try {
            // Store or update product
            $this->storeOrUpdateProduct($api_product, $this->platform_id, $this->credential);
            DB::commit();

            $progress = ProgressTracking::find($this->progress_id);
            if ($progress && $progress->status === 'running') {
                $progress->increment('processed');

                // calculate % if total > 0
                if ($progress->total > 0 && $progress->processed >= $progress->total) {
                    $progress->status = 'completed';
                }

                $progress->save();
            }

        } catch (\Throwable $e) {
            DB::rollBack();
            logger()->error("Product sync failed (platform {$this->platform_id}, product ID: {$api_product->id}): " . $e->getMessage());
        }
    }

    logger()->info("Page {$this->page} processed for platform {$this->platform_id}");

    // --- Final safety check after loop ---
    ProgressTracking::where('id', $this->progress_id)
        ->where('status', 'running')
        ->whereColumn('processed', '>=', 'total')
        ->update(['status' => 'completed']);
}



}


