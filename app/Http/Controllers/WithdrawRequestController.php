<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\WithdrawRequest;
use App\Models\BaseCurrency;
use App\Models\OpenPosition;
use App\Models\CronJob;
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
}
