<?php

use App\Jobs\TrefleBackup;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

// TODO: is there a cleaner way to perform async recurrent jobs
// TODO: wtf does this->comment do
Artisan::command('trefle-backup', function () {
    $backupAgent = new TrefleBackup;
    $backupAgent->handle();
})->purpose('Perform a backup of Trefle into our base models');
