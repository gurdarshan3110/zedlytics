<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateTransferClientsFromCSV extends Command
{
    protected $signature = 'update:transfer-clients-from-csv {file}';
    protected $description = 'Transfer clients from a CSV file';

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

        // Remove header row
        $header = array_shift($data);
        
        foreach ($data as $row) {
            $accountId = $row['account id'];
            $newParentId = $row['Transfer to'];

            $client = Client::where('client_code', $accountId)->first();
            if ($client) {
                $client->transfered_to = $newParentId;
                $client->transfered_from = $client->parentId;
                $client->transfered = 1;
                $client->save();

                $this->info("Client with account ID $accountId updated successfully.");
            } else {
                $this->warn("Client with account ID $accountId not found.");
            }
        }

        $this->info('Client updates from CSV completed.');
        return 0;
    }
}
