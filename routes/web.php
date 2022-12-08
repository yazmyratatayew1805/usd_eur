<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ValutaController;


Route::get('/', [ValutaController::class, 'show']);
Route::resource('valutas', ValutaController);
