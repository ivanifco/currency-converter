<?php

use app\Http\Controllers\CurrencyController;




Route::post('/convert', [CurrencyController::class, 'convert']);