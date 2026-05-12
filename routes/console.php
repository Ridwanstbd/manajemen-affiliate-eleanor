<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('creator:auto-blacklist')->dailyAt('00:00');
Schedule::command('app:sync-delivery-status')->everyTwoHours();