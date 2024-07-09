<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use App\Models\Client;
use App\Models\Account;
use App\Models\ClientAccount;

class LoginApiProvider extends ServiceProvider
{
    protected $token;
    protected $clientTreeUserIdNode;

    public function register()
    {
        $this->login();

        $this->app->singleton('LoginApiProvider', function ($app) {
            return $this;
        });
    }

    public function login()
    {
        $response = Http::post('https://bestbullapi.arktrader.io/api/apigateway/login/public/api/v1/login', [
            'companyName' => 'Best Bull',
            'password' => env('BESTBULL_PASSWORD'),
            'userName' => env('BESTBULL_USERNAME'),
        ]);

        $data = $response->json();
        $this->token = $data['data']['token'];
        $this->clientTreeUserIdNode = $data['data']['clientTreeUserIdNode'][0];
    }

    public function getUserById($userId)
    {
        $response = Http::withToken($this->token)->get("https://bestbullapi.arktrader.io/api/apigateway/admin/public/api/v1/user/{$userId}");
        return $response->json();
    }

    public function createClientAndAccountFromUserId($userId)
    {
        try {
            $data = $this->getUserById($userId);
            dd($data);
            if ($data && $data['success']) {
                $userData = $data['data'];

                // Create a new Client instance
                $client = Client::create([
                    'client_code' => $userData['accountId'],
                    'name' => $userData['firstName'],
                    'phone_no' => $userData['mobile'] ?? $userData['id'],
                    'email' => $userData['username'] . '@zedlytics.com', // Assuming email is not provided, using username as a placeholder
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
        } catch (\Exception $e) {
            // Handle the exception (log it, notify someone, etc.)
            Log::error('Failed to create client and account: ' . $e->getMessage());
            throw $e;
        }
    }
}
