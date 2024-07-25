<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\TrxLog;
use App\Models\CronJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TransactionLogsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            set_time_limit(1800);
            $response = Http::post('https://bestbullapi.arktrader.io/api/apigateway/login/public/api/v1/login', [
                'companyName' => 'Best Bull',
                'password' => env('BESTBULL_PASSWORD'),
                'userName' => env('BESTBULL_USERNAME'),
            ]);

            $data = $response->json();
            CronJob::create(['cron_job_name' => 'Transaction Log API']);
            $this->token = $data['data']['token'];
            $this->clientTreeUserIdNode = $data['data']['clientTreeUserIdNode'][0];
            $fromDate = '2024-07-23 21:30:00';
            $toDate = '2024-07-24 21:29:59';
            //$fromDate = Carbon::now()->subSeconds(2);
            //$toDate = Carbon::now()->toDateTimeString();
            $response = Http::timeout(180)->withToken($this->token)->get("https://bestbullapi.arktrader.io/api/apigateway/admin/public/api/v1/user/".$this->clientTreeUserIdNode."/transactionLogs?fromDate=".$fromDate."&toDate=".$toDate."&ticketOrderId=&trxLogActionTypeId=&trxLogTransTypeId=&trxSubTypeId=&ipAddress=&createdById=");
            if ($response->successful()) {
                $clientDatas = $response->json()['data'];
                foreach ($clientDatas as $key => $clientData) {
                    $clientData['ark_id'] = $clientData['id'];
                    $trxLog = TrxLog::updateOrCreate(
                        [
                            'ticketOrderId' => $clientData['ticketOrderId'],
                            'ark_id' => $clientData['ark_id'],
                        ],
                        $clientData
                    );
                } 

            } else {
                // Handle API call failure
                // Log the error or take appropriate actions
                \Log::error("Failed to update transaction log: " . $response->body());
            }
            
        } catch (\Exception $e) {
            // Handle any exceptions
            Log::error('Failed to create client and account: ' . $e->getMessage());
        }
    }
}
