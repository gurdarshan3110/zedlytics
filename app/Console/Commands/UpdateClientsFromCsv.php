<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use Illuminate\Support\Facades\Log;

class UpdateClientsFromCsv extends Command
{
    protected $signature = 'update:clients-from-csv {file}';
    protected $description = 'Update clients email and phone_no from a CSV file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $filePath = $this->argument('file');

        if (filter_var($filePath, FILTER_VALIDATE_URL)) {
            $this->info("Downloading file from URL: $filePath");
            $response = Http::get($filePath);

            if ($response->failed()) {
                $this->error("Failed to download file from URL: $filePath");
                return 1;
            }

            $filePath = sys_get_temp_dir() . '/' . basename($filePath);
            File::put($filePath, $response->body());
        }

        //$filePath = $this->argument('file');

        if (!File::exists($filePath)) {
            $this->error("File not found: $filePath");
            return 1;
        }

        if (!File::isReadable($filePath)) {
            $this->error("File is not readable: $filePath");
            return 1;
        }

        $header = null;
        $data = [];

        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        foreach ($data as $row) {
            $row['client_code']=$row['ACCOUNT ID'];
            $row['email']=$row['EMAIL'];
            $row['phone_no']=$row['MOBILE'];
            //dd($row);
            if (isset($row['client_code']) && !empty($row['client_code'])) {
                $client = Client::where('client_code', $row['client_code'])->first();

                if ($client) {
                    $updateData = [];
                    if (!empty($row['email'])) {
                        $updateData['email'] = $row['email'];
                    }
                    if (!empty($row['phone_no'])) {
                        $updateData['phone_no'] = $row['phone_no'];
                    }

                    if (!empty($updateData)) {
                        $client->update($updateData);
                        $this->info("Updated client_code: {$row['client_code']}");
                    }
                } else {
                    Log::warning("Client not found for client_code: {$row['client_code']}");
                }
            }
        }

        $this->info('Client updates completed.');
    }
}
