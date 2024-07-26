<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\ClientCurrencyPolicy as Model;
use App\Models\CronJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ClientCurrencyPolicyJob implements ShouldQueue
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
            
            $response = Http::timeout(30)->withToken($this->token)->get($this->baseUrl."admin/public/api/v1/client/currency/policy");

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
                CronJob::create(['cron_job_name' => 'Client Currency Policy Api']);
            } else {
                // Handle API call failure
                // Log the error or take appropriate actions
                \Log::error("Failed to update client currency policy" . $response->body());
            }
            
        } catch (\Exception $e) {
            // Handle any exceptions
            Log::error('Failed to create Client Currency Policies: ' . $e->getMessage());
        }
    }
}
