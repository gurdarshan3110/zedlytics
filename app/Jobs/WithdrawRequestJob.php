<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\WithdrawRequest;
use App\Models\CronJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WithdrawRequestJob implements ShouldQueue
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
            // Login to the API once
            
            $response = Http::withToken($this->token)->get($this->baseUrl.'admin/public/api/v1/cashDelivery/pending/' . $this->clientTreeUserIdNode);
            if ($response->successful()) {
                CronJob::create(['cron_job_name' => 'Open Withdraw Request API']);
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
            } else {
                // Handle API call failure
                // Log the error or take appropriate actions
                \Log::error("Failed to update client " . $response->body());
            }
            
        } catch (\Exception $e) {
            // Handle any exceptions
            Log::error('Failed to create client and account: ' . $e->getMessage());
        }
    }
}
