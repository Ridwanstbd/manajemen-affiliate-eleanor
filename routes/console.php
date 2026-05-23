<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('creator:auto-blacklist')->hourly();
Schedule::command('app:sync-delivery-status')->everySixHours();