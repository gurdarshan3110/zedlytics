<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Jobs\ProcessBatchJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FetchAndProcessData extends Command
{
    protected $signature = 'fetch:process-data';
    protected $description = 'Fetch data from API and process it in batches';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $response = Http::post('https://bestbullapi.arktrader.io/api/apigateway/login/public/api/v1/login', [
                'companyName' => 'Best Bull',
                'password' => env('BESTBULL_PASSWORD'),
                'userName' => env('BESTBULL_USERNAME'),
            ]);

            $data = $response->json();
            $token = $data['data']['token'];
            $clientTreeUserIdNode = $data['data']['clientTreeUserIdNode'][0];

            $batchSize = 5;  // Adjust based on API limits and server capacity
            $totalRecords = 86400;  // Total number of records
            $iterations = ceil($totalRecords / $batchSize);

            $fromDate = Carbon::now()->startOfDay()->toDateTimeString();

            for ($i = 0; $i < $iterations; $i++) {
                $toDate = Carbon::parse($fromDate)->addSeconds($batchSize)->toDateTimeString();

                $response = Http::timeout(120)->retry(5, 1000)->withToken($token)->get("https://bestbullapi.arktrader.io/api/apigateway/admin/public/api/v1/user/".$clientTreeUserIdNode."/transactionLogs?fromDate=".$fromDate."&toDate=".$toDate."&ticketOrderId=&trxLogActionTypeId=&trxLogTransTypeId=&trxSubTypeId=&ipAddress=&createdById=");
                Log::info("https://bestbullapi.arktrader.io/api/apigateway/admin/public/api/v1/user/".$clientTreeUserIdNode."/transactionLogs?fromDate=".$fromDate."&toDate=".$toDate."&ticketOrderId=&trxLogActionTypeId=&trxLogTransTypeId=&trxSubTypeId=&ipAddress=&createdById=");
                if ($response->successful()) {
                    Log::info($response);
                    $records = $response->json()['data'];
                    Log::info($records);
                    // Dispatch job to process this batch
                    ProcessBatchJob::dispatch($records);
                } else {
                    $this->error("Failed to fetch records: " . $response->body());
                }

                $fromDate = $toDate;
            }
        } catch (\Exception $e) {
            $this->error('Failed to fetch and process data: ' . $e->getMessage());
        }
    }
}
