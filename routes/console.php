<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('creator:auto-blacklist')->dailyAt('00:00');