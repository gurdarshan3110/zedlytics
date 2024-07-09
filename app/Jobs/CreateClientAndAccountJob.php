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
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CreateClientAndAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    /**
     * Create a new job instance.
     *
     * @param  int  $userId
     * @return void
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Log::info('Client and Account created successfully.');
        try {
            // Login to the API
            $loginResponse = Http::post('https://bestbullapi.arktrader.io/api/apigateway/login/public/api/v1/login', [
                'companyName' => 'Best Bull',
                'password' => env('BESTBULL_PASSWORD'),
                'userName' => env('BESTBULL_USERNAME'),
            ]);

            $loginData = $loginResponse->json();
            $token = $loginData['data']['token'];
            $clientTreeUserIdNode = $loginData['data']['clientTreeUserIdNode'][0];

            // Fetch user data by userId
            $getUserResponse = Http::withToken($token)->get("https://bestbullapi.arktrader.io/api/apigateway/admin/public/api/v1/user/{$this->userId}");
            $userData = $getUserResponse->json();

            if ($getUserResponse->successful() && $userData['success']) {
                $userData = $userData['data'];
                $client = Client::where('user_id',$userData['id'])->first();
                if(empty($client) || $client==null){
                    // Create a new Client instance
                    $client = Client::create([
                        'client_code' => $userData['accountId'],
                        'user_id' => $userData['id'],
                        'name' => $userData['firstName'],
                        'phone_no' => $userData['mobile'] ?? $userData['id'],
                        'email' => $userData['username'] . '@zedlytics.com', // Placeholder email
                        'status' => $userData['locked'] ? '1' : '0',
                    ]);

                    // Create a new Account instance
                    $account = Account::create([
                        'account_code' => $userData['accountId'],
                        'name' => $userData['firstName'],
                        'type' => Account::CLIENT_ACCOUNT,
                        'status' => $userData['locked'] ? '1' : '0',
                    ]);

                    // Associate the client with the account
                    ClientAccount::create([
                        'client_id' => $client->id,
                        'account_id' => $account->id,
                    ]);         

                }
            } else {
                // Log failure if unable to fetch user data
                Log::error('Failed to fetch user data or user not found.');
            }
        } catch (\Exception $e) {
            // Handle any exceptions
            Log::error('Failed to create client and account: ' . $e->getMessage());
        }
    }
}
