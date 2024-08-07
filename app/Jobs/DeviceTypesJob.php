<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\UserDevice;
use App\Models\CronJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DeviceTypesJob implements ShouldQueue
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
            $cronJob = CronJob::where('cron_job_name','Device IP/MAC')->latest()->first();

            $cronjob = CronJob::create([
                'cron_job_name' => 'Device IP/MAC',
            ]);
           
            $response = Http::timeout(360)->withToken($this->token)->get($this->baseUrl."admin/public/api/v1/user/get/live/users");
            if ($response->successful()) {
                $clientDatas = $response->json()['data'];
                foreach ($clientDatas as $key => $clientData) {
                    $getMac = $this->getMac($clientData['deviceType']);
                    
                    $clientData['device_type'] = $getMac['device_type'];
                    $clientData['user_id'] = $clientData['userId'];
                    $clientData['address_type'] = 0;
                    
                    $trxLog = UserDevice::updateOrCreate(
                        [
                            'user_id' => $clientData['userId'],
                            'client_address' => $clientData['ipAddress'],
                            'device_type' => $clientData['device_type'],
                        ],
                        $clientData
                    );
                    $clientData['client_address'] = $getMac['mac_id'];
                    $clientData['address_type'] = 1;
                    $trxLog = UserDevice::updateOrCreate(
                        [
                            'user_id' => $clientData['userId'],
                            'client_address' => $clientData['client_address'],
                            'device_type' => $clientData['device_type'],
                        ],
                        $clientData
                    );
                } 
                $cronjob->status = 1;
                $cronjob->save();
            } else {
                // Handle API call failure
                // Log the error or take appropriate actions
                Log::error("Failed to update device type: " . $response->body());
            }
            
        } catch (\Exception $e) {
            // Handle any exceptions
            Log::error('Failed to add user device: ' . $e->getMessage());
        }
    }

    public function getCronTime($start_time, $end_time) {
        if ($start_time == null && $end_time == null) {
            return array(
                'from_date' => '2024-08-06 00:00:00',
                'to_date'   => '2024-08-06 00:03:59'
            );
        }

        if ($start_time != null) {
            $start_time = Carbon::parse($end_time)->subMinutes(1)->format('Y-m-d H:i:s');
            //$start_time = Carbon::now('Asia/Kolkata')->sub(2)->setTimezone('Asia/Riyadh')->format('Y-m-d H:i:s');
        }

        if ($end_time != null) {
            $end_time = Carbon::parse($end_time)->addMinutes(59)->format('Y-m-d H:i:s');
            //$end_time = Carbon::now('Asia/Kolkata')->setTimezone('Asia/Riyadh')->format('Y-m-d H:i:s');
        }

        return array(
            'start_time' => $start_time,
            'end_time'   => $end_time
        );
    }

    public function getMac($device){
        $type = explode('@', $device);
        return [
                    'device_type'  => $type[0],
                    'mac_id'  => $type[1]
                ];
    }

}
