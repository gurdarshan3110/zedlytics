<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class UpdateCleintsBrandCsv extends Command
{
    protected $signature = 'update:clients-brand-csv {file}';
    protected $description = 'Update clients brand from a CSV file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $filePath = $this->argument('file');

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
            $row['client_code']=$row['Account Id'];
            $row['brand']=$row['Brand'];
            //dd($row);
            if (isset($row['client_code']) && !empty($row['client_code'])) {
                $client = Client::where('client_code', $row['client_code'])->first();

                if ($client) {
                    $updateData = [];
                    if (!empty($row['brand'])) {
                        $updateData['brand_id'] = (($row['brand']=='SKY')?1:'');
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
