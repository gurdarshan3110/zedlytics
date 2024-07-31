<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\Client;
use App\Models\CronJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TransferUserJob implements ShouldQueue
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
            
            $cronjob = CronJob::create([
                'cron_job_name' => 'Transfer Users API'
            ]);
            $clients = Client::select('user_id','transfered_to','id')->where('transfered',1)->get();
            foreach ($clients as $key => $client) {
                $data = [
                    'toParentNodeUserId' => $client->transfered_to,
                    'userId' => $client->user_id,
                ];

                $response = Http::timeout(360)
                    ->withToken($this->token)
                    ->put($this->baseUrl . "admin/public/api/v1/user", $data);

                if ($response->successful()) {
                    $clientUser = Client::find($client->id);
                    $clientUser->transfered = 0;
                    $clientUser->parentId = $clientUser->transfered_to;
                    $clientUser->save();
                } else {
                    Log::error('API request failed', [
                        'client' => $client,
                        'response' => $response->body(),
                    ]);
                }
            }
            $cronjob->status = 1;
            $cronjob->save();
            
        } catch (\Exception $e) {
            // Handle any exceptions
            Log::error('Failed to transfer User: ' . $e->getMessage());
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
            //$start_time = '2024-07-31 09:00:00';
            $start_time = Carbon::now('Asia/Kolkata')->subMinutes(2)->setTimezone('Asia/Riyadh')->format('Y-m-d H:i:s');
        }

        if ($end_time != null) {
            //$end_time = Carbon::now()->format('Y-m-d H:i:s');
            //$end_time = '2024-07-31 09:59:59';
            $end_time = Carbon::now('Asia/Kolkata')->setTimezone('Asia/Riyadh')->format('Y-m-d H:i:s');
        }

        return array(
            'start_time' => $start_time,
            'end_time'   => $end_time
        );
    }

}
