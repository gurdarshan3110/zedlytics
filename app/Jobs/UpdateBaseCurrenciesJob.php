<?php

namespace App\Jobs;

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

class UpdateBaseCurrenciesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $token;
    protected $baseUrl;
    protected $clientTreeUserIdNode;

    public function __construct()
    {
        $this->baseUrl = config('services.bestbull.base_url');
    }

    public function handle()
    {
        $username = config('services.bestbull.username');
        $password = config('services.bestbull.password');

        $response = Http::post($this->baseUrl.'login/public/api/v1/login', [
            'companyName' => 'Best Bull',
            'password' => $password,
            'userName' => $username,
        ]);

        if ($response->failed()) {
            // Handle login failure (e.g., log the error or notify someone)
            return;
        }

        $data = $response->json();
        $this->token = $data['data']['token'] ?? null;
        $this->clientTreeUserIdNode = $data['data']['clientTreeUserIdNode'][0] ?? null;

        if (!$this->token || !$this->clientTreeUserIdNode) {
            // Handle missing token or clientTreeUserIdNode
            return;
        }
        $cronjob = CronJob::create(['cron_job_name' => 'Update Base Currencies']);
    
        $response = Http::withToken($this->token)->get($this->baseUrl.'admin/public/api/v1/currency');
        if ($response->failed()) {
            // Handle fetch failure (e.g., log the error or notify someone)
            return;
        }

        $data = $response->json();

        foreach ($data['data'] as $item) {
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
        $cronjob->status = 1;
        $cronjob->save();
    }
}
