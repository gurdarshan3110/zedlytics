<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\FetchOpenPositionsJob;
use App\Jobs\CreateClientAndAccountJob;
use App\Jobs\TransactionLogsJob;
use App\Jobs\CreateNewClientsJob;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();
//Schedule::job(new TransactionLogsJob, 'default')->everyTwoSeconds();
Schedule::job(new FetchOpenPositionsJob, 'default')->everyFourMinutes();
Schedule::job(new CreateNewClientsJob, 'default')->everyTwoMinutes();
Schedule::job(new CreateClientAndAccountJob, 'default')->everyFiveMinutes();
Schedule::job(new TransactionLogsJob, 'default')->everyTenMinutes();

//Schedule::job(new FetchOpenPositionsJob, 'openpositions', 'sqs')->everyFiveMinutes();
