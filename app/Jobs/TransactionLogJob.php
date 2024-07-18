<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\TxnLog;
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
            $response = Http::post('https://bestbullapi.arktrader.io/api/apigateway/login/public/api/v1/login', [
                'companyName' => 'Best Bull',
                'password' => env('BESTBULL_PASSWORD'),
                'userName' => env('BESTBULL_USERNAME'),
            ]);

            $data = $response->json();
            $this->token = $data['data']['token'];
            $this->clientTreeUserIdNode = $data['data']['clientTreeUserIdNode'][0];
            // Login to the API once
            CronJob::create(['cron_job_name' => 'Update Transaction Log']);

            $clients = Client::where('client_code', 2)->where('status', 0)->get();
            $currentDate = now()->endOfDay()->toDateString();
            foreach ($clients as $client) {
                $response = Http::withToken($this->token)->get("https://bestbullapi.arktrader.io/api/apigateway/admin/public/api/v1/user/20597/transactionLogs?fromDate=2020-01-01 00:00:00&toDate=".$currentDate."&ticketOrderId=&trxLogActionTypeId=&trxLogTransTypeId=&trxSubTypeId=&ipAddress=&createdById=");

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
                    \Log::error("Failed to update client with user_id {$client->user_id}: " . $response->body());
                }
            }
            
        } catch (\Exception $e) {
            // Handle any exceptions
            Log::error('Failed to create client and account: ' . $e->getMessage());
        }
    }
}
