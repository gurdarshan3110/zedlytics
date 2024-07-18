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
            set_time_limit(900);
            $response = Http::post('https://bestbullapi.arktrader.io/api/apigateway/login/public/api/v1/login', [
                'companyName' => 'Best Bull',
                'password' => env('BESTBULL_PASSWORD'),
                'userName' => env('BESTBULL_USERNAME'),
            ]);

            $data = $response->json();
            CronJob::create(['cron_job_name' => 'Transaction Log API']);
            $this->token = $data['data']['token'];
            $this->clientTreeUserIdNode = $data['data']['clientTreeUserIdNode'][0];

            $currentDate = Carbon::now()->endOfDay()->toDateTimeString();
            Log::info("https://bestbullapi.arktrader.io/api/apigatewayadmin/public/api/v1/user/".$this->clientTreeUserIdNode."/transactionLogs?fromDate=2024-07-18%2000:00:00&toDate=2024-07-18%2010:59:59&ticketOrderId=&trxLogActionTypeId=&trxLogTransTypeId=&trxSubTypeId=&ipAddress=&createdById=");
            // $response = Http::timeout(60)->withToken($this->token)->get("https://bestbullapi.arktrader.io/api/apigateway/admin/public/api/v1/user/".$this->clientTreeUserIdNode."/transactionLogs?fromDate=2024-07-17 00:00:00&toDate=2024-07-17 02:59:59&ticketOrderId=&trxLogActionTypeId=&trxLogTransTypeId=&trxSubTypeId=&ipAddress=&createdById=");
            // if ($response->successful()) {

            //     $clientDatas = $response->json()['data'];
            //     foreach ($clientDatas as $key => $clientData) {
            //         Log::info($clientData);
            //         $trxLog = TrxLog::updateOrCreate(
            //             ['ark_id' => $clientData['id']],
            //             $clientData
            //         );
            //     } 

            // } else {
            //     // Handle API call failure
            //     // Log the error or take appropriate actions
            //     \Log::error("Failed to update transaction log: " . $response->body());
            // }
            
        } catch (\Exception $e) {
            // Handle any exceptions
            Log::error('Failed to create client and account: ' . $e->getMessage());
        }
    }
}
