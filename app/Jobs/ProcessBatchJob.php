<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\TrxLog;
use Illuminate\Support\Facades\Log;

class ProcessBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $records;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($records)
    {
        $this->records = $records;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            foreach ($this->records as $record) {
                TrxLog::updateOrCreate(
                    ['ticketOrderId' => $record['ticketOrderId']],
                    $record
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to process batch: ' . $e->getMessage());
        }
    }
}
