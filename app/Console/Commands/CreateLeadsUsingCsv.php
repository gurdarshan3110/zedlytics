<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lead;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CreateLeadsUsingCsv extends Command
{
    protected $signature = 'create:leads-using-csv {file}';
    protected $description = 'Create Leads from a CSV file';

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
            //dd($row);
            $data = $row['name'];
            $pattern = '/^([a-zA-Z0-9]+): (\d{10}) ([\w\s]+)(?: \((\w)\))?(?=\s+[a-zA-Z0-9]+:|\s*$)/m';

            $id='';
            $phone='';
            $name='';
            $status='';
            //echo $data;
            if (preg_match($pattern, $data, $matches)) {
                // Extract the matched groups
                $id = $matches[1];
                $phone = $matches[2];
                $name = $matches[3];
                $status = $matches[4] ?? '';

                //echo "Phone: " . $phone . "\n";
            } else {
                if($data !='name'){
                    echo $data;
                    echo "No match found." . "\n";//exit;
                }
            }
            $lead = Lead::updateOrCreate(
                            [
                                'phone_no' => $phone,
                                'name' => $name
                            ],
                            [
                                'account_id' => $id,
                                'phone_no' => $phone,
                                'name' => $name,
                                'status' => (($status=='')?1:0)
                            ]
                        );
        }

        $this->info('Leads insertion from CSV completed.');
        return 0;
    }
}
