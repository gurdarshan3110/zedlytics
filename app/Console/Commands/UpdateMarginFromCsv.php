<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MarginLimitMarket;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateMarginFromCsv extends Command
{
    protected $signature = 'update:margin-from-csv {file}';
    protected $description = 'Update';

    public function __construct()
    {
        parent::__construct();
    }

     public function handle()
    {
        $filePath = $this->argument('file');

        // Download file if it's a URL
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

        // Check if file exists and is readable
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

        // Read the CSV file
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

        // Insert data into the database
        foreach ($data as $row) {
            MarginLimitMarket::create($row); // Fixed here
        }

        $this->info('Client updates completed.');
        return 0; // Indicate success
    }
}
