<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\FetchOpenPositionsJob;
use App\Jobs\CreateClientAndAccountJob;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::job(new FetchOpenPositionsJob, 'openpositions', 'sqs')->everyFourMinutes();
Schedule::job(new CreateClientAndAccountJob, 'createclients', 'sqs')->everyThirtyMinutes();
//Schedule::job(new FetchOpenPositionsJob, 'openpositions', 'sqs')->everyFiveMinutes();
