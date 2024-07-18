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

class TransactionLogJob implements ShouldQueue
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
            set_time_limit(15000);
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
            $response = Http::timeout(60)->withToken($this->token)->get("https://bestbullapi.arktrader.io/api/apigateway/admin/public/api/v1/user/".$this->clientTreeUserIdNode."/transactionLogs?fromDate=2022-07-01 00:00:00&toDate=2021-07-18 23:59:59&ticketOrderId=&trxLogActionTypeId=&trxLogTransTypeId=&trxSubTypeId=&ipAddress=&createdById=");

            // Handle the response as needed
            if ($response->successful()) {

                $clientData = $response->json()['data'];
                $trxLog = TrxLog::updateOrCreate(
                    ['id' => $clientData['id']],
                    $clientData
                );

                

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
