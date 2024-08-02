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
use App\Jobs\TransactionLogsJob;
use App\Jobs\CreateNewClientsJob;
use App\Jobs\ClientCurrencyPolicyJob;
use App\Jobs\ClientGenericPolicyJob;
use App\Jobs\RoboDealerPolicyJob;
use App\Jobs\AgentCommissionPolicyJob;
use App\Jobs\AccountMirroringPolicyJob;
use App\Jobs\WithdrawRequestJob;
use App\Jobs\CreateDealerJob;
use App\Jobs\TransferUserJob;
use App\Jobs\UpdateBaseCurrenciesJob;
use Carbon\Carbon;

class WithdrawRequestController extends Controller
{
    private $token;
    private $clientTreeUserIdNode;
    protected $baseUrl;

    public function login()
    {
        $username =config('services.bestbull.username');
        $password =config('services.bestbull.password');
        $this->baseUrl =config('services.bestbull.base_url');
        $response = Http::post($this->baseUrl.'login/public/api/v1/login', [
            'companyName' => 'Best Bull',
            'password' => $username,
            'userName' => $password,
        ]);

        $data = $response->json();
        //dd($this->baseUrl.'login/public/api/v1/login');
        $this->token = $data['data']['token'];
        $this->clientTreeUserIdNode = $data['data']['clientTreeUserIdNode'][0];
    }

    public function fetchWithdrawRequests()
    {
        WithdrawRequestJob::dispatch();
        return response()->json(['message' => 'Withdraw request job dispatched successfully.']);
    }

    public function fetchBaseCurrencies()
    {
        UpdateBaseCurrenciesJob::dispatch();
        return response()->json(['message' => 'Base Currencies job dispatched successfully.']);
    }

    public function fetchTransferUser()
    {
        TransferUserJob::dispatch();
        return response()->json(['message' => 'Transfer User job dispatched successfully.']);
    }

    public function pushWithdrawRequestsToDB()
    {
        $this->fetchWithdrawRequests();
        return response()->json(['message' => 'Withdraw requests fetched and updated successfully.']);
    }

    public function fetchOpenPositions()
    {
        FetchOpenPositionsJob::dispatch();
        return response()->json(['message' => 'Open positions fetching job dispatched successfully.']);
    }

    public function fetchNewClients()
    {
        CreateNewClientsJob::dispatch();
        return response()->json(['message' => 'New Clients job dispatched successfully.']);
    }

    public function fetchTransactionLog()
    {
        TransactionLogsJob::dispatch();
        return response()->json(['message' => 'New Transaction log job dispatched successfully.']);
    }

    public function fetchNewDealers()
    {
        CreateDealerJob::dispatch();
        return response()->json(['message' => 'New Dealers job dispatched successfully.']);
    }

    public function fetchClientCurrencyPolicies()
    {
        ClientCurrencyPolicyJob::dispatch();
        return response()->json(['message' => 'Client Currency policies dispatched successfully.']);
    }

    public function fetchClientGenericPolicies()
    {
        ClientGenericPolicyJob::dispatch();
        return response()->json(['message' => 'Client Generic policies dispatched successfully.']);
    }

    public function fetchRoboDealerPolicies()
    {
        RoboDealerPolicyJob::dispatch();
        return response()->json(['message' => 'Robo Dealer policies dispatched successfully.']);
    }

    public function fetchAccountMirroringPolicies()
    {
        AccountMirroringPolicyJob::dispatch();
        return response()->json(['message' => 'Robo Dealer policies dispatched successfully.']);
    }

    public function fetchAgentCommissionPolicies()
    {
        AgentCommissionPolicyJob::dispatch();
        return response()->json(['message' => 'Agent Commission policies dispatched successfully.']);
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
        CreateNewClientsJob::dispatch();
        return response()->json(['message' => 'New Client dispatched successfully.']);
    }
}
