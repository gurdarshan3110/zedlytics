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
            $username =config('services.bestbull.username');
            $password =config('services.bestbull.password');
            $this->baseUrl =config('services.bestbull.base_url');
            $response = Http::post($this->baseUrl.'login/public/api/v1/login', [
                'companyName' => 'Best Bull',
                'password' => $password,
                'userName' => $username,
            ]);

            $data = $response->json();
            $this->token = $data['data']['token'];
            $this->clientTreeUserIdNode = $data['data']['clientTreeUserIdNode'][0];
            // Login to the API once
            CronJob::create(['cron_job_name' => 'New Clients API']);

            //$startDate = Carbon::now()->subMinutes(10);
            $startDate = '2024-07-23 00:00:00';
            $endDate = Carbon::now()->endOfDay()->toDateTimeString();
            $response = Http::timeout(60)->withToken($this->token)->get($this->baseUrl."admin/public/api/v1/report/users/details/".$this->clientTreeUserIdNode."?fromDate=".$startDate."&toDate=".$endDate);

            // Handle the response as needed
            if ($response->successful()) {

                $clientDatas = $response->json()['data'];
                foreach ($clientDatas as $key => $clientData) {
                    $client = Client::where('user_id',$clientData['userID'])->first();
                    if(empty($client) || $client==null){
                        $clientData['client_code'] = $clientData['accountID'];
                        $clientData['user_id'] = $clientData['userID'];
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
                }
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
