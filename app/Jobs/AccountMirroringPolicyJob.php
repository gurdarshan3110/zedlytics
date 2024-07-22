<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\AccountMirroringPolicy as Model;
use App\Models\CronJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AccountMirroringPolicyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $response = Http::post($this->baseUrl.'login/public/api/v1/login', [
                'companyName' => 'Best Bull',
                'password' => env('BESTBULL_PASSWORD'),
                'userName' => env('BESTBULL_USERNAME'),
            ]);

            $data = $response->json();
            $this->token = $data['data']['token'];
            $this->clientTreeUserIdNode = $data['data']['clientTreeUserIdNode'][0];
            // Login to the API once
            
            $response = Http::timeout(30)->withToken($this->token)->get($this->baseUrl."admin/public/api/v1/accountMirroringPolicy");

            // Handle the response as needed
            if ($response->successful()) {

                $records = $response->json()['data'];
                foreach ($records as $key => $data) {
                    $data['ark_id'] = $data['id'];
                    $model = Model::updateOrCreate(
                            [
                                'ark_id' => $data['id'],
                                'policyName' => $data['policyName'],
                                'policyTypeId' => $data['policyTypeId'],
                                'parentId' => $data['parentId']
                            ],
                            $data,
                        );
                    
                }
                CronJob::create(['cron_job_name' => 'Account Mirroring Policy Api']);
            } else {
                // Handle API call failure
                // Log the error or take appropriate actions
                \Log::error("Failed to update acount mirroring policy " . $response->body());
            }
            
        } catch (\Exception $e) {
            // Handle any exceptions
            Log::error('Failed to create Account Mirroring Policies: ' . $e->getMessage());
        }
    }
}
