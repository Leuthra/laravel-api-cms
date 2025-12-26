<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

// not used, because we use api.php
// require __DIR__.'/auth.php';
