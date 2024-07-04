<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\WithdrawRequest;
use App\Models\BaseCurrency;
use App\Models\OpenPosition;
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
        //dd($data['data']['token']);
        $this->token = $data['data']['token'];
        $this->clientTreeUserIdNode = $data['data']['clientTreeUserIdNode'][0];
    }

    public function fetchWithdrawRequests()
    {
        $this->login();

        $response = Http::withToken($this->token)->get('https://bestbullapi.arktrader.io/api/apigateway/admin/public/api/v1/cashDelivery/pending/'. $this->clientTreeUserIdNode);
        
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

    public function fetchBaseCurrencyData()
    {
        $this->login();

        $response = Http::withToken($this->token)->get('https://bestbullapi.arktrader.io/api/apigateway/admin/public/api/v1/currency');
        
        $data = $response->json();

        foreach ($data['data'] as $item) {
            if($item['used']!=0){
                BaseCurrency::updateOrCreate(
                    ['base_id' => $item['id']],
                    [
                        'name' => $item['name'],
                        'used' => $item['used'],
                        'open_day' => $item['openDay'],
                        'close_day' => $item['closeDay'],
                        'open_time' => $item['openTime'],
                        'close_time' => $item['closeTime'],
                        'daily_close_time_from1' => $item['dailyCloseTimeFrom1'],
                        'daily_close_time_to1' => $item['dailyCloseTimeTo1'],
                        'daily_close_time_from2' => $item['dailyCloseTimeFrom2'],
                        'daily_close_time_to2' => $item['dailyCloseTimeTo2'],
                        'daily_close_time_from3' => $item['dailyCloseTimeFrom3'],
                        'daily_close_time_to3' => $item['dailyCloseTimeTo3'],
                        'tick_digits' => $item['tickDigits'],
                        'closed' => $item['closed'],
                        'reference_currency_id' => $item['referenceCurrencyId'],
                        'decimal_digits' => $item['decimalDigits'],
                        'sell_only' => $item['sellOnly'],
                        'buy_only' => $item['buyOnly'],
                        'description' => $item['description'],
                        'currency_type_id' => $item['currencyTypeId'],
                        'parent_id' => $item['parentId'],
                        'amount_unit_id' => $item['amountUnitId'],
                        'row_color' => $item['rowColor'],
                        'auto_stop_trade' => $item['autoStopTrade'],
                        'auto_stop_trade_seconds' => $item['autoStopTradeSeconds'],
                        'requotable' => $item['requotable'],
                        'move_if_closed' => $item['moveIfClosed'],
                        'spread_from_bid' => $item['spreadFromBid'],
                        'feeder_name' => $item['feederName'],
                        'expiry_date' => $item['expiryDate'],
                        'contract_size' => $item['contractSize'],
                        'direct_calculation' => $item['directCalculation'],
                        'ref_direct_calculation' => $item['refDirectCalculation'],
                        'close_cancel_all_on_expiry' => $item['closeCancelAllOnExpiry'],
                        'auto_cancel_sltp_orders' => $item['autoCancelSltpOrders'],
                        'auto_cancel_entry_orders' => $item['autoCancelEntryOrders'],
                        'auto_switch_feed_seconds' => $item['autoSwitchFeedSeconds'],
                    ]
                );
            }
        }

        return response()->json(['message' => 'Base currency data fetched and updated successfully.']);
    }

    public function fetchOpenPositions()
    {
        $this->login();
        // $fromDate = Carbon::now()->subMinute()->format('Y-m-d H:i:s');
        // $toDate = Carbon::now()->addMinutes(5)->format('Y-m-d H:i:s');
        OpenPosition::truncate();
        BaseCurrency::truncate();
        $this->fetchBaseCurrencyData();
        $fromDate = '2020-01-01 00:00:00';
        //$fromDate = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
        $toDate = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');

        $response = Http::withToken($this->token)->get('https://bestbullapi.arktrader.io/api/apigateway/trading/public/api/v1/report/open/positions/' . $this->clientTreeUserIdNode . '/0?currencyIds=&withDemo=false&fromDate='.$fromDate.'&toDate='.$toDate);

        $data = $response->json();
        //dd($data);
        foreach ($data['data'] as $item) {
            OpenPosition::updateOrCreate(
                ['ticketID' => $item['ticketID']],
                [
                    'userID' => $item['userID'],
                    'posCurrencyID' => $item['posCurrencyID'],
                    'posDate' => $item['posDate'],
                    'openAmount' => $item['openAmount'],
                    'closeAmount' => $item['closeAmount'],
                    'posPrice' => $item['posPrice'],
                    'posType' => $item['posType'],
                    'openCommission' => $item['openCommission'],
                    'currentPrice' => $item['currentPrice'],
                    'referenceCurrencyId' => $item['referenceCurrencyId'],
                    'posComment' => $item['posComment'] ?? null,
                ]
            );
        }

        return response()->json(['message' => 'Open positions data fetched and updated successfully.']);
    }
}