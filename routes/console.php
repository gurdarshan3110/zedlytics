<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\FetchOpenPositionsJob;
use App\Jobs\CreateClientAndAccountJob;
use App\Jobs\TransactionLogsJob;
use App\Jobs\CreateNewClientsJob;
use App\Jobs\WithdrawRequestJob;
use App\Jobs\TransactionsReLogJob;
use App\Jobs\TransferUserJob;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();
//Schedule::job(new TransactionLogsJob, 'default')->everyTwoSeconds();
Schedule::job(new FetchOpenPositionsJob, 'default')->everyFourMinutes();
Schedule::job(new WithdrawRequestJob, 'default')->everyTwoMinutes();
Schedule::job(new CreateNewClientsJob, 'default')->everyTwoMinutes();
//Schedule::job(new CreateClientAndAccountJob, 'default')->everyFiveMinutes();
Schedule::job(new TransactionLogsJob, 'default')->everyMinute();
Schedule::job(new TransactionsReLogJob, 'default')->everyThreeMinutes();
Schedule::job(new TransferUserJob, 'default')->twiceDaily(10, 17);
//Schedule::job(new FetchOpenPositionsJob, 'openpositions', 'sqs')->everyFiveMinutes();
