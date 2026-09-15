<?php

use Illuminate\Foundation\Inspiring;
use App\Jobs\VerificarManutencaoJob;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::job(new VerificarManutencaoJob)->dailyAt('02:00');
