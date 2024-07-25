<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\TrxLog;
use App\Models\CronJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TransactionLogsJob implements ShouldQueue
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
            set_time_limit(3600);
            $response = Http::post('https://bestbullapi.arktrader.io/api/apigateway/login/public/api/v1/login', [
                'companyName' => 'Best Bull',
                'password' => env('BESTBULL_PASSWORD'),
                'userName' => env('BESTBULL_USERNAME'),
            ]);

            $data = $response->json();
            
            $this->token = $data['data']['token'];
            $this->clientTreeUserIdNode = $data['data']['clientTreeUserIdNode'][0];    
            $fDate=null;        
            $tDate=null;        
            $cronJob = CronJob::where('cron_job_name','Transactions Log API')->latest()->first();
            if($cronJob){
                $fDate=$cronJob['start_time'];        
                $tDate=$cronJob['end_time'];
            }
            //dd($fDate);
            $getTime = $this->getCronTime($fDate,$tDate);
            $fromDate = $getTime['from_date'];
            $toDate = $getTime['to_date'];
            //dd('From Date '.$cronJob);
            Log::info('To Date '.$toDate);
            $cronjob = CronJob::create([
                'cron_job_name' => 'Transactions Log API',
                'start_time' => $fromDate,
                'end_time' => $toDate,
            ]);
            
            $response = Http::timeout(360)->withToken($this->token)->get("https://bestbullapi.arktrader.io/api/apigateway/admin/public/api/v1/user/".$this->clientTreeUserIdNode."/transactionLogs?fromDate=".$fromDate."&toDate=".$toDate."&ticketOrderId=&trxLogActionTypeId=&trxLogTransTypeId=&trxSubTypeId=&ipAddress=&createdById=");
            if ($response->successful()) {
                $clientDatas = $response->json()['data'];
                foreach ($clientDatas as $key => $clientData) {
                    $clientData['ark_id'] = $clientData['id'];
                    $trxLog = TrxLog::updateOrCreate(
                        [
                            'ticketOrderId' => $clientData['ticketOrderId'],
                            'ark_id' => $clientData['ark_id'],
                        ],
                        $clientData
                    );
                } 

            } else {
                // Handle API call failure
                // Log the error or take appropriate actions
                Log::error("Failed to update transaction log: " . $response->body());
            }
            
        } catch (\Exception $e) {
            // Handle any exceptions
            Log::error('Failed to create transaction: ' . $e->getMessage());
        }
    }

    public function getCronTime($start_time, $end_time) {
        if ($start_time == null && $end_time == null) {
            return array(
                'from_date' => '2022-07-06 00:00:00',
                'to_date'   => '2022-07-06 11:59:59'
            );
        }

        if ($start_time != null) {
            $start_time = date('Y-m-d H:i:s', strtotime($start_time . ' +1 day'));
        }

        if ($end_time != null) {
            $end_time = date('Y-m-d H:i:s', strtotime($end_time . ' +1 day'));
        }

        return array(
            'from_date' => $start_time,
            'to_date'   => $end_time
        );
    }

}
