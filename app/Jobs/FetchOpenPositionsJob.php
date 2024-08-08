<?php

namespace App\Jobs;

use App\Models\OpenPosition;
use App\Models\CronJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Client;

class FetchOpenPositionsJob implements ShouldQueue
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
        set_time_limit(-1);
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

        OpenPosition::truncate();
        CronJob::create(['cron_job_name' => 'Open Position']);
        
        //$this->fetchBaseCurrencyData();

        //$fromDate = '2020-01-01 00:00:00';
        $fromDate = Carbon::now()->subDays(60)->endOfDay()->format('Y-m-d H:i:s');
        $toDate = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');

        $response = Http::timeout(360)->withToken($this->token)->get($this->baseUrl.'trading/public/api/v1/report/open/positions/' . $this->clientTreeUserIdNode . '/0', [
            'currencyIds' => '',
            'withDemo' => false,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        ]);

        if ($response->failed()) {
            // Handle fetch failure (e.g., log the error or notify someone)
            return;
        }

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
        }
    }
}
