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
            set_time_limit(-1);
            $username =config('services.bestbull.username');
            $password =config('services.bestbull.password');
            $this->baseUrl =config('services.bestbull.base_url');
            $response = Http::post($this->baseUrl.'login/public/api/v1/login', [
                'companyName' => 'Best Bull',
                'password' => $password,
                'userName' => $username,
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
            $fromDate = $getTime['start_time'];
            $toDate = $getTime['end_time'];
            //dd('From Date '.$cronJob);
            //Log::info('To Date '.$toDate);
            $cronjob = CronJob::create([
                'cron_job_name' => 'Transactions Log API',
                'start_time' => $fromDate,
                'end_time' => $toDate,
            ]);
            Log::info($this->baseUrl."admin/public/api/v1/user/".$this->clientTreeUserIdNode."/transactionLogs?fromDate=".$fromDate."&toDate=".$toDate."&ticketOrderId=&trxLogActionTypeId=&trxLogTransTypeId=&trxSubTypeId=&ipAddress=&createdById=");
            $response = Http::timeout(360)->withToken($this->token)->get($this->baseUrl."admin/public/api/v1/user/".$this->clientTreeUserIdNode."/transactionLogs?fromDate=".$fromDate."&toDate=".$toDate."&ticketOrderId=&trxLogActionTypeId=&trxLogTransTypeId=&trxSubTypeId=&ipAddress=&createdById=");
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
                $cronjob->status = 1;
                $cronjob->save();
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
                'from_date' => '2024-07-26 00:00:00',
                'to_date'   => '2024-07-26 00:59:59'
            );
        }

        if ($start_time != null) {
            //$start_time = Carbon::now()->subMinutes(2)->format('Y-m-d H:i:s');
            $start_time = '2024-07-31 09:00:00';
        }

        if ($end_time != null) {
            //$end_time = Carbon::now()->format('Y-m-d H:i:s');
            $end_time = '2024-07-31 10:59:59';
        }

        return array(
            'start_time' => $start_time,
            'end_time'   => $end_time
        );
    }

}
