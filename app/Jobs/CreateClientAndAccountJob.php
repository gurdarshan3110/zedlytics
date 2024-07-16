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
use Illuminate\Support\Facades\Log;

class CreateClientAndAccountJob implements ShouldQueue
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
            CronJob::create(['cron_job_name' => 'Update Client Info Job']);

            $clients = Client::where('client_code', 2)->where('status', 0)->get();

            foreach ($clients as $client) {
                $response = Http::withToken($this->token)->post("https://bestbullapi.arktrader.io/api/apigateway/admin/public/api/v1/user/{$client->user_id}");

                // Handle the response as needed
                if ($response->successful()) {

                    $clientData = $response->json()['data'];

                    // Update client information
                    $clientData['client_code'] = $clientData['accountID'];
                    $clientData['phone_no'] = $clientData['accountID'].$clientData['userID'];
                    $clientData['email'] = $clientData['userID'].'@zedlytics.com';
                    $clientData['name'] = $clientData['firstName'];
                    $clientData['status'] = 0;
                    $client = Client::updateOrCreate(
                        ['user_id' => $clientData['userID']],
                        $clientData
                    );
                    $clientData['type'] = Account::CLIENT_ACCOUNT;
                    $account = Account::updateOrCreate(
                        ['account_code' => $clientData['client_code']],
                        $clientData
                    );

                    $map = ClientAccount::updateOrCreate(
                        ['account_id' => $account['id']],
                        ['client_id'=>$client['id']]
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
