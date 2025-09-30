<?php

use Illuminate\Support\Facades\Route;
use app\Http\Controllers\CurrencyController;


Route::post('/convert', [CurrencyController::class, 'convert']);