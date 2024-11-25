<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaxCalculatorController;

Route::get('/', [TaxCalculatorController::class, 'index']);
Route::post('/calculate', [TaxCalculatorController::class, 'calculate'])->name('calculate');
Route::get('/exception', function () {
    return view('exception');
})->name('exception');
