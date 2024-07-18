<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\Client;
use App\Models\Account;
use App\Models\ClientAccount;
use App\Models\CronJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CreateNewClientsJob implements ShouldQueue
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
            

            $startDate = Carbon::now()->subMinutes(15);
            $endDate = Carbon::now()->endOfDay()->toDateTimeString();
            $response = Http::withToken($this->token)->get("https://bestbullapi.arktrader.io/api/apigateway/admin/public/api/v1/report/users/details/".$this->clientTreeUserIdNode."?fromDate=".$startDate."&toDate=".$endDate);

            // Handle the response as needed
            if ($response->successful()) {

                $clientDatas = $response->json()['data'];
                foreach ($clientDatas as $key => $clientData) {
                    $clientData['client_code'] = $clientData['accountID'];
                    $clientData['name'] = $clientData['firstName'];
                    $clientData['phone_no'] = $clientData['accountID'].$clientData['userID'];
                    $clientData['email'] = $clientData['userID'].'@zedlytics.com';
                    $client = Client::updateOrCreate(
                        ['user_id' => $clientData['userID']],
                        $clientData
                    );

                    $clientData['status'] = 0;
                    $clientData['type'] = Account::CLIENT_ACCOUNT;
                    $account = Account::updateOrCreate(
                        ['account_code' => $client['client_code']],
                        $clientData
                    );

                    $map = ClientAccount::updateOrCreate(
                        ['account_id' => $account['id']],
                        ['client_id'=>$client['id']]
                    );
                }
                CronJob::create(['cron_job_name' => 'New Clients API']);
            } else {
                // Handle API call failure
                // Log the error or take appropriate actions
                \Log::error("Failed to update client " . $response->body());
            }
            
        } catch (\Exception $e) {
            // Handle any exceptions
            Log::error('Failed to create client and account: ' . $e->getMessage());
        }
    }
}
