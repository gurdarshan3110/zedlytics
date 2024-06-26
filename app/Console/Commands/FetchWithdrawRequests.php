<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\WithdrawRequestController;

class FetchWithdrawRequests extends Command
{
    // The name and signature of the console command.
    protected $signature = 'fetch:withdraw-requests';

    // The console command description.
    protected $description = 'Fetch withdraw requests and update the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $controller = new WithdrawRequestController();
        $controller->fetchWithdrawRequests();

        $this->info('Withdraw requests fetched and updated successfully.');
        
        return 0;
    }
}
