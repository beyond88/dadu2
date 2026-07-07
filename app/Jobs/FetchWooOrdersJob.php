<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Traits\Woocommerce;
use Illuminate\Support\Facades\DB;
use App\Models\ProgressTracking;

class FetchWooOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Woocommerce;

    public $tries = 5;
    public $maxExceptions = 6;
    public $timeout = 2000;

    protected $orders;
    protected $platform_id;
    protected $credential;
    protected $page;
    protected $progress_id;

    public function __construct($orders, $platform_id, $credential, $page, $progress_id)
    {
        $this->orders = $orders;
        $this->platform_id = $platform_id;
        $this->credential = $credential;
        $this->page = $page;
        $this->progress_id = $progress_id;
        logger("Order job created for page {$this->page}");
    }

    public function handle()
    {
        logger("Order job called for page {$this->page}");

        foreach ($this->orders as $order) {
            try {
                DB::beginTransaction();

                // Save or update order
                $this->storeOrUpdateOrder($order, $this->platform_id);

                DB::commit();

                // --- Progress tracking ---
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
                logger()->error("Order sync failed (platform {$this->platform_id}): " . $e->getMessage());
            }
        }

        logger()->info("Page {$this->page} processed for platform {$this->platform_id}");
    }
}
