<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('db:backup')->daily();
