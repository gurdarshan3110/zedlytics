<?php

namespace App\Jobs;

use App\Models\OpenPosition;
use App\Models\BaseCurrency;
use App\Models\CronJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Client;
use App\Models\Account;
use App\Models\ClientAccount;

class FetchOpenPositionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //private $token;
    //private $clientTreeUserIdNode;

    public function __construct()
    {
        
    }

    public function handle()
    {
        $response = Http::post('https://bestbullapi.arktrader.io/api/apigateway/login/public/api/v1/login', [
            'companyName' => 'Best Bull',
            'password' => env('BESTBULL_PASSWORD'),
            'userName' => env('BESTBULL_USERNAME'),
        ]);

        $data = $response->json();
        $this->token = $data['data']['token'];
        $this->clientTreeUserIdNode = $data['data']['clientTreeUserIdNode'][0];

        OpenPosition::truncate();
        BaseCurrency::query()->delete();
        CronJob::create(['cron_job_name' => 'Open Position']);
        
        $this->fetchBaseCurrencyData();

        $fromDate = '2020-01-01 00:00:00';
        $toDate = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');

        $response = Http::withToken($this->token)->get('https://bestbullapi.arktrader.io/api/apigateway/trading/public/api/v1/report/open/positions/' . $this->clientTreeUserIdNode . '/0?currencyIds=&withDemo=false&fromDate=' . $fromDate . '&toDate=' . $toDate);

        $data = $response->json();

        foreach ($data['data'] as $item) {
            OpenPosition::updateOrCreate(
                ['ticketID' => $item['ticketID']],
                [
                    'userID' => $item['userID'],
                    'posCurrencyID' => $item['posCurrencyID'],
                    'posDate' => $item['posDate'],
                    'openAmount' => $item['posType'] == 1 ? $item['openAmount'] : -$item['openAmount'],
                    'closeAmount' => $item['closeAmount'],
                    'posPrice' => $item['posPrice'],
                    'posType' => $item['posType'],
                    'openCommission' => $item['openCommission'],
                    'currentPrice' => $item['currentPrice'],
                    'referenceCurrencyId' => $item['referenceCurrencyId'],
                    'posComment' => $item['posComment'] ?? null,
                    'status' => 0,
                ]
            );
            if ($item['closeAmount'] != 0) {
                OpenPosition::updateOrCreate(
                    ['ticketID' => $item['ticketID'] . '1'],
                    [
                        'userID' => $item['userID'],
                        'posCurrencyID' => $item['posCurrencyID'],
                        'posDate' => $item['posDate'],
                        'openAmount' => $item['posType'] == 1 ? -$item['closeAmount'] : $item['closeAmount'],
                        'closeAmount' => 0,
                        'posPrice' => $item['posPrice'],
                        'posType' => $item['posType'] == 1 ? 2 : 1,
                        'openCommission' => $item['openCommission'],
                        'currentPrice' => $item['currentPrice'],
                        'referenceCurrencyId' => $item['referenceCurrencyId'],
                        'posComment' => $item['posComment'] ?? null,
                        'status' => 1,
                    ]
                );
            }
            // $client = Client::where('user_id',$item['userID'])->first();
            // if(empty($client) || $client==null){
            //     $client = Client::create([
            //         'client_code' => 2,
            //         'user_id' => $item['userID'],
            //         'name' => $item['userID'],
            //         'phone_no' => $item['userID'].$item['userID'] ,
            //         'email' => $item['userID'] . '@zedlytics.com',
            //         'status' => 0,
            //     ]);
            // }
            
        }
    }

    protected function fetchBaseCurrencyData()
    {
        $response = Http::withToken($this->token)->get('https://bestbullapi.arktrader.io/api/apigateway/admin/public/api/v1/currency');
        
        $data = $response->json();

        foreach ($data['data'] as $item) {
            if ($item['used'] != 0) {
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
    }
}
