<?php

use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});

Route::get('/weather-location', [WeatherController::class, 'show']);

Route::post('/register-notify', [WeatherController::class, 'registerNotify']);

Route::get('/verify', [WeatherController::class, 'confirmEmail']);

Route::get('/weather-detail', [WeatherController::class, 'weatherDetail']);

Route::get('/weather-detail-ajax', [WeatherController::class, 'weatherDetailAjax']);
