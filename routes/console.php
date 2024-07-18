<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\FetchOpenPositionsJob;
use App\Jobs\CreateClientAndAccountJob;
use App\Jobs\TransactionLogJob;
use App\Jobs\CreateNewClientsJob;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::job(new FetchOpenPositionsJob, 'default')->everyFourMinutes();
Schedule::job(new CreateNewClientsJob, 'default')->everyTwoMinutes();
Schedule::job(new CreateClientAndAccountJob, 'default')->everyFiveMinutes();
Schedule::job(new TransactionLogJob, 'default')->everyThreeMinutes();

//Schedule::job(new FetchOpenPositionsJob, 'openpositions', 'sqs')->everyFiveMinutes();
