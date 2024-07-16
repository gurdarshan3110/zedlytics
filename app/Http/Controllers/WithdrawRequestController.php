<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\WithdrawRequest;
use App\Models\BaseCurrency;
use App\Models\OpenPosition;
use App\Models\CronJob;
use App\Models\Client;
use App\Models\ClientAccount;
use App\Models\Account;
use App\Jobs\FetchOpenPositionsJob;
use Carbon\Carbon;

class WithdrawRequestController extends Controller
{
    private $token;
    private $clientTreeUserIdNode;

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

    public function fetchWithdrawRequests()
    {
        $this->login();

        $response = Http::withToken($this->token)->get('https://bestbullapi.arktrader.io/api/apigateway/admin/public/api/v1/cashDelivery/pending/' . $this->clientTreeUserIdNode);
        
        $data = $response->json();
        WithdrawRequest::where('status', 0)->update(['status' => 1]);
        foreach ($data['data'] as $item) {
            WithdrawRequest::updateOrCreate(
                ['request_id' => $item['requestId']],
                [
                    'comment' => $item['comment'],
                    'request_status' => $item['status'],
                    'amount' => $item['amount'],
                    'branch_id' => $item['branchId'],
                    'request_date' => $item['requestDate'],
                    'user_id' => $item['userId'],
                    'status' => 0,
                ]
            );
        }
    }

    public function pushWithdrawRequestsToDB()
    {
        $this->fetchWithdrawRequests();
        return response()->json(['message' => 'Withdraw requests fetched and updated successfully.']);
    }

    public function fetchOpenPositions()
    {
        $this->login();
        FetchOpenPositionsJob::dispatch($this->token, $this->clientTreeUserIdNode);
        return response()->json(['message' => 'Open positions fetching job dispatched successfully.']);
    }

    public function fetchAndSaveClientRecords()
    {
        set_time_limit(900);
        $this->login();

        $response = Http::withToken($this->token)->get('https://bestbullapi.arktrader.io/api/apigateway/admin/public/api/v1/report/users/details/'. $this->clientTreeUserIdNode);
        
        if ($response->getStatusCode() == 200) {
            $data = $response->json();

            if (is_array($data['data'])) {
                foreach ($data['data'] as $clientData) {
                    //dd($clientData);
                    $clientData['client_code'] = $clientData['accountID'];
                    $clientData['phone_no'] = $clientData['accountID'].$clientData['userID'];
                    $clientData['email'] = $clientData['userID'].'@zedlytics.com';
                    $clientData['name'] = $clientData['firstName'];
                    $clientData['status'] = (($clientData['locked'])?1:0);
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


                }

                return response()->json([
                    'success' => true,
                    'message' => 'Client records fetched and saved successfully'
                ], 201);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch client records'
        ], 500);
    }

    public function fetchAndInsertClientRecords()
    {
        $this->login();
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
                $clientData['country'] = $clientData['country'];
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
    }
}
